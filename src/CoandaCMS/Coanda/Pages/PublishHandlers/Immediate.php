<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

use Coanda;

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

    public function display($data)
    {
        return 'Published immediately';
    }

    /**
     * @param $version
     * @param $data
     */
    public function validate($version, $data)
	{
	}

    /**
     * @param $version
     * @param $data
     * @param $pageRepository
     * @param $urlRepository
     */
    public function execute($version, $data, $pageRepository, $urlRepository)
	{
		$pageRepository->publishVersion($version, Coanda::currentUser()->id, $urlRepository);
	}
}