<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

use Coanda;
use CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException;

class Immediate implements PublishHandlerInterface {

	public $identifier = 'immediate';
	public $name = 'Publish Immediately';

	public $template = 'coanda::admin.pages.publishoptions.immediate';

	public function validate()
	{
		// Nothing to validate on this one
	}

	public function execute($version, $urlRepository, $historyRepository)
	{
		$page = $version->page;

		if ($version->version !== 1)
		{
			// set the current published version to be archived
			$page->currentVersion()->status = 'archived';
			$page->currentVersion()->save();			
		}

		// set this version to be published
		$version->status = 'published';
		$version->save();
		
		// update the page name attribute (via the type)
		$page->name = $page->pageType()->generateName($version);
		$page->current_version = $version->version;
		$page->save();

		// Register the URL for this version with the Url Repo
		$url = $urlRepository->register($version->base_slug . $version->slug, 'page', $page->id);

		// Log the history
		$historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'publish_version', ['version' => (int)$version->version]);		
	}
}