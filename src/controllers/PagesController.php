<?php namespace CoandaCMS\Coanda\Controllers;

use View, Redirect;

use CoandaCMS\Coanda\Exceptions\PageVersionNotFound;

class PagesController extends BaseController {

	private $pageRepository;

	public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository)
	{
		$this->pageRepository = $pageRepository;
	}

	public function getPreview($preview_key)
	{
		try
		{
			$version = $this->pageRepository->getVersionByPreviewKey($preview_key);

			return View::make('coanda::pages.preview', [ 'version' => $version ]);
		}
		catch(PageVersionNotFound $exception)
		{
			return Redirect::to('/');
		}
	}

}