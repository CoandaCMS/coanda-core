<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

use CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException;
use Carbon\Carbon;

class Delayed implements PublishHandlerInterface {

    /**
     * @var string
     */
    public $identifier = 'delayed';
    /**
     * @var string
     */
    public $name = 'Delay publishing until a specified date and time';

    /**
     * @var string
     */
    public $template = 'coanda::admin.modules.pages.publishoptions.delayed';

    public function display($data)
    {
        $handler_data = json_decode($data, true);
        $publish_date = Carbon::createFromFormat($handler_data['format'], $handler_data['date'], $handler_data['timezone']);

        return 'Delayed until: ' . $publish_date;
    }

    /**
     * @param $version
     * @param $data
     * @return mixed|void
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException
     */
    public function validate($version, $data)
	{
		if (!isset($data['delayed_publish_date']) || $data['delayed_publish_date'] == '')
		{
			throw new PublishHandlerException(['delayed_publish_date' => 'Please specify a date']);
		}

		$format = isset($data['date_format']) ? $data['date_format'] : false;

		try
		{
			$date = Carbon::createFromFormat($format, $data['delayed_publish_date'], date_default_timezone_get());

			if ($date->isPast())
			{
				throw new PublishHandlerException(['delayed_publish_date' => 'The specified date is in past']);
			}
		}
		catch (\InvalidArgumentException $exception)
		{
			throw new PublishHandlerException(['delayed_publish_date' => 'The specified date is invalid']);
		}
	}

    public function execute($version, $data, $pageRepository, $urlRepository)
	{
		$format = isset($data['date_format']) ? $data['date_format'] : false;

		$handler_data = [
            'format' => $format,
			'date' => $data['delayed_publish_date'],
            'timezone' => date_default_timezone_get()
		];

        $handler_data = $this->reserveNewSlug($handler_data, $version, $urlRepository);

		$version->publish_handler_data = json_encode($handler_data);
		$version->status = 'pending';
		$version->save();
	}

    private function reserveNewSlug($handler_data, $version, $urlRepository)
    {
        $current_slug = $version->page->slug;

        if ($version->full_slug !== $current_slug)
        {
            $url = $urlRepository->register($version->full_slug, 'pendingversion', $version->id);

            $handler_data['reserved_url'] = $url->id;
        }

        return $handler_data;
    }

    /**
     * @param $command
     * @param $pageRepository
     * @param $urlRepository
     */
    public static function executeFromCommand($command, $pageRepository, $urlRepository)
	{
		$offset = 0;
		$limit = 5;

		// Put the versions to be published into a separate array, otherwise the offset/limit logic will not work
		$publish_version_list = [];

		// Loop until we run out of versions...
		while(true)
		{
	        // Get any pending versions
	        $pending_versions = $pageRepository->getPendingVersions($offset, $limit);

	        if ($pending_versions->count() == 0)
	        {
	        	break;
	        }

	        foreach ($pending_versions as $pending_version)
	        {
	            // Do this version need to be published?
	            $handler_data = json_decode($pending_version->publish_handler_data, true);
                $publish_date = Carbon::createFromFormat($handler_data['format'], $handler_data['date'], $handler_data['timezone']);

                if ($publish_date->isPast())
                {
                    $publish_version_list[] = $pending_version->id;
                }
                else
                {
                    $command->info('Version #' . $pending_version->version . ' of page #' . $pending_version->page->id . ' is not due to be published yet.');
                }
	        }

	        $offset += $limit;
		}

		foreach ($publish_version_list as $publish_version_id)
		{
			try
			{
				$version = $pageRepository->getVersionById($publish_version_id);

	            $handler_data = json_decode($version->publish_handler_data, true);

				// Remove the 'reserved' url....
	            if (isset($handler_data['reserved_url']))
	            {
	            	$url = $urlRepository->findFor('pendingversion', $version->id);

	            	// Just double check that we still have the right reserved url...
	            	if ($url->id == $handler_data['reserved_url'])
	            	{
	            		// Delete the URL - so that the publish routine, below, can use it.
	            		$url->delete();
	            	}
	            }

				$pageRepository->publishVersion($version, $version->edited_by, $urlRepository);

				$command->info('Version #' . $version->version . ' of page #' . $version->page->id . ' published');
			}
			catch (PageVersionNotFound $exception)
			{
				$command->error('Page version id: ' . $publish_version_id . ' not found');
			}
		}

		$command->info('All done.');
	}
}