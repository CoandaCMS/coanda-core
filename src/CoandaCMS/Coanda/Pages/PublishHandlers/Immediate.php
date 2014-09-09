<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

use Coanda;

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
     * @return mixed|void
     */
    public function validate($version, $data)
	{
		// Nothing to validate on this one
	}

    /**
     * @param $version
     * @param $data
     * @param $pageRepository
     * @param $urlRepository
     * @param $historyRepository
     * @return mixed|void
     */
    public function execute($version, $data, $pageRepository, $urlRepository, $historyRepository)
	{
		$pageRepository->publishVersion($version, Coanda::currentUser()->id, $urlRepository, $historyRepository);
	}
}