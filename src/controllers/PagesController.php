<?php namespace CoandaCMS\Coanda\Controllers;

use View, Redirect, App, Coanda, Input, Session;

use CoandaCMS\Coanda\Exceptions\Pages\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\ValidationException;
/**
 * Class PagesController
 * @package CoandaCMS\Coanda\Controllers
 */
class PagesController extends BaseController {

    /**
     * @var \CoandaCMS\Coanda\Pages\Factory\PageFactoryInterface
     */
    private $pageFactory;

    /**
     * @param \CoandaCMS\Coanda\Pages\Factory\PageFactoryInterface $pageFactory
     */
    public function __construct(\CoandaCMS\Coanda\Pages\Factory\PageFactoryInterface $pageFactory)
	{
		$this->pageFactory = $pageFactory;
	}

    /**
     * @param $preview_key
     * @return mixed
     */
    public function getPreview($preview_key)
	{
		try
		{
			$version = $this->pageFactory->getVersionByPreviewKey($preview_key);
			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			return View::make('coanda::pages.preview', [ 'version' => $version, 'preview_key' => $preview_key, 'invalid_fields' => $invalid_fields ]);
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
			$version = $this->pageFactory->getVersionByPreviewKey($preview_key);

			return Coanda::module('pages')->renderVersion($version);
		}
		catch(PageVersionNotFound $exception)
		{
			return App::abort('404');
		}
	}

	public function postPreviewComment($preview_key)
	{
		try
		{
			$version = $this->pageFactory->getVersionByPreviewKey($preview_key);

			$this->pageFactory->addVersionComment($version, Input::all());

			return Redirect::to('pages/preview/' . $preview_key)->with('comment_saved', true);
		}
		catch(PageVersionNotFound $exception)
		{
			return App::abort('404');
		}
		catch(ValidationException $exception)
		{
			return Redirect::to('pages/preview/' . $preview_key)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}

}