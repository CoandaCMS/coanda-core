<?php namespace CoandaCMS\Coanda\Controllers;

use View, Redirect, App, Coanda, Input, Session;

use CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\ValidationException;
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
			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			return View::make('coanda::pages.preview', [ 'version' => $version, 'preview_key' => $preview_key, 'invalid_fields' => $invalid_fields ]);
		}
		catch (PageVersionNotFound $exception)
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
		catch (PageVersionNotFound $exception)
		{
			return App::abort('404');
		}
	}

	public function postPreviewComment($preview_key)
	{
		try
		{
			$version = $this->pageRepository->getVersionByPreviewKey($preview_key);

			$this->pageRepository->addVersionComment($version, Input::all());

			return Redirect::to('pages/preview/' . $preview_key)->with('comment_saved', true);
		}
		catch (PageVersionNotFound $exception)
		{
			return App::abort('404');
		}
		catch (ValidationException $exception)
		{
			return Redirect::to('pages/preview/' . $preview_key)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}

}