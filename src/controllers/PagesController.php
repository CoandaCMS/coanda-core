<?php namespace CoandaCMS\Coanda\Controllers;

use View, Redirect, App, Coanda;

use CoandaCMS\Coanda\Exceptions\PageVersionNotFound;

/**
 * Class PagesController
 * @package CoandaCMS\Coanda\Controllers
 */
class PagesController extends BaseController {

    /**
     * @var \CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @param \CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository
     */
    public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository)
	{
		$this->pageRepository = $pageRepository;
	}

    /**
     * @param $preview_key
     * @return mixed
     */
    public function getPreview($preview_key)
	{
		try
		{
			$version = $this->pageRepository->getVersionByPreviewKey($preview_key);

			return View::make('coanda::pages.preview', [ 'version' => $version, 'preview_key' => $preview_key ]);
		}
		catch(PageVersionNotFound $exception)
		{
			return App::abort('404');
		}
	}

	public function getRenderPreview($preview_key, $location = false)
	{
		try
		{
			$version = $this->pageRepository->getVersionByPreviewKey($preview_key);

			return Coanda::module('pages')->renderVersion($version);
		}
		catch(PageVersionNotFound $exception)
		{
			return App::abort('404');
		}

	}

}