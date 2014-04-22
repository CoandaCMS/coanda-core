<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

use Coanda;
use CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException;

class Immediate implements PublishHandlerInterface {

	public $identifier = 'immediate';
	public $name = 'Publish Immediately';

	public $template = 'coanda::admin.pages.publishoptions.immediate';

	public function validate($version, $data)
	{
		// Nothing to validate on this one
	}

	public function execute($version, $data, $pageRepository, $urlRepository, $historyRepository)
	{
		$pageRepository->publishVersion($version, Coanda::currentUser()->id, $urlRepository, $historyRepository);
	}
}