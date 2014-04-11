<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

use Coanda;
use CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException;
use Carbon\Carbon;

class Delayed implements PublishHandlerInterface {

	public $identifier = 'delayed';
	public $name = 'Delay publishing until a specified date and time';

	public $template = 'coanda::admin.pages.publishoptions.delayed';

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

	public function execute($version, $data, $urlRepository, $historyRepository)
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
			$url = $urlRepository->register($version->base_slug . $version->slug, 'pendingpage', $page->id);

			$handler_data['reserved_url'] = $url->id;
		}

		$version->publish_handler_data = json_encode($handler_data);

		// Set the version to be pending...
		$version->status = 'pending';
		$version->save();

		// Log the history
		$historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'publish_version_delayed', ['version' => (int)$version->version, 'date' => $date]);
	}
}