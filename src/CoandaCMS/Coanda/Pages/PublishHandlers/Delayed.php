<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

use Coanda;
use CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException;
use Carbon\Carbon;

/**
 * Class Delayed
 * @package CoandaCMS\Coanda\Pages\PublishHandlers
 */
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

    /**
     * @param $version
     * @param $data
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException
     */
    public function validate($version, $data)
	{
		if (!isset($data['delayed_publish_date']) || $data['delayed_publish_date'] == '')
		{
			throw new PublishHandlerException(['delayed_publish_date' => 'Please specfiy a date']);
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
		catch(\InvalidArgumentException $exception)
		{
			throw new PublishHandlerException(['delayed_publish_date' => 'The specified date is invalid']);
		}
	}

    /**
     * @param $version
     * @param $data
     * @param $pageRepository
     * @param $urlRepository
     * @param $historyRepository
     */
    public function execute($version, $data, $pageRepository, $urlRepository, $historyRepository)
	{
		$page = $version->page;

		$format = isset($data['date_format']) ? $data['date_format'] : false;
		$date = Carbon::createFromFormat($format, $data['delayed_publish_date'], date_default_timezone_get());

		$handler_data = [
			'date' => $date
		];

		$current_version_slug = $page->currentVersion()->slug;

		if ($version->slug !== $current_version_slug)
		{
			// Register the URL for this for a pendingpage, so it doesn't get used by someone else
			$url = $urlRepository->register($version->base_slug . $version->slug, 'pendingversion', $version->id);

			$handler_data['reserved_url'] = $url->id;
		}

		$version->publish_handler_data = json_encode($handler_data);

		// Set the version to be pending...
		$version->status = 'pending';
		$version->save();

		// Log the history
		$historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'publish_version_delayed', ['version' => (int)$version->version, 'date' => $date]);
	}

    /**
     * @param $command
     * @param $pageRepository
     * @param $urlRepository
     * @param $historyRepository
     */
    public static function executeFromCommand($command, $pageRepository, $urlRepository, $historyRepository)
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
	            $handler_data = json_decode($pending_version->publish_handler_data);

	            if (isset($handler_data->date))
	            {
	            	$publish_date = Carbon::createFromFormat('Y-m-d H:i:s', $handler_data->date->date, $handler_data->date->timezone);

	            	if ($publish_date->isPast())
	            	{
	            		$publish_version_list[] = $pending_version->id;
	            	}
	            	else
	            	{
	            		$command->error('Version id: ' . $pending_version->id . ' is not due to be published yet.');
	            	}
	            }
	        }

	        $offset += $limit;
		}

		foreach ($publish_version_list as $publish_version_id)
		{
			try
			{
				$version = $pageRepository->getVersionById($publish_version_id);

	            $handler_data = json_decode($version->publish_handler_data);

				// Remove the 'reserved' url....
	            if (isset($handler_data->reserved_url))
	            {
	            	$url = $urlRepository->findFor('pendingversion', $version->id);

	            	// Just double check that we still have the right reserved url...
	            	if ($url->id == $handler_data->reserved_url)
	            	{
	            		// Delete the URL - so that the publish routine, below, can use it.
	            		$url->delete();
	            	}
	            }

				$pageRepository->publishVersion($version, false, $urlRepository, $historyRepository);

				$command->info('Version #' . $version->version . ' of page #' . $version->page->id . ' published');
			}
			catch (\CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound $exception)
			{
				$command->error('Page version id: ' . $publish_version_id . ' not found');
			}
		}

		$command->info('All done.');
	}
}