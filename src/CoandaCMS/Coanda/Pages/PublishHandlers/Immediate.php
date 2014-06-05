<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

use Coanda;
use CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException;

/**
 * Class Immediate
 * @package CoandaCMS\Coanda\Pages\PublishHandlers
 */
class Immediate implements PublishHandlerInterface {

    /**
     * @var string
     */
    public $identifier = 'immediate';
    /**
     * @var string
     */
    public $name = 'Publish Immediately';

    /**
     * @var string
     */
    public $template = 'coanda::admin.modules.pages.publishoptions.immediate';

    /**
     * @param $version
     * @param $data
     */
    public function validate($version, $data)
	{
		// Nothing to validate on this one
	}

    /**
     * @param $version
     * @param $data
     * @param $pageFactory
     * @param $urlRepository
     * @param $historyRepository
     */
    public function execute($version, $data, $pageFactory, $urlRepository, $historyRepository)
	{
		$pageFactory->publishVersion($version, Coanda::currentUser()->id, $urlRepository, $historyRepository);
	}
}