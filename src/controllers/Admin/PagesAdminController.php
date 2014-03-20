<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input;

use CoandaCMS\Coanda\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\ValidationException;

use CoandaCMS\Coanda\Controllers\BaseController;

class PagesAdminController extends BaseController {

	private $pageRepository;

	public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository)
	{
		$this->pageRepository = $pageRepository;

		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		return View::make('coanda::admin.pages.index');
	}

	public function getView($id)
	{
		try
		{
			$page = $this->pageRepository->find($id);

			return View::make('coanda::admin.pages.view', ['page' => $page]);			
		}
		catch(PageNotFound $exception)
		{
			App::abort('404');
		}
	}

	public function getCreate($page_type)
	{
		try
		{
			$type = Coanda::getPageType($page_type);
			$page = $this->pageRepository->create($type, Coanda::currentUser()->id);

			// Redirect to edit (version 1 - which should be the only version, give this is the create method!)
			return Redirect::to(Coanda::adminUrl('pages/edit/' . $page->id . '/1'));
		}
		catch (PageTypeNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}

	}

	public function getEdit($page_id, $version_number)
	{
		try
		{
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);

			return View::make('coanda::admin.pages.edit', ['version' => $version]);
		}
		catch(PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
		catch(PageVersionNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
		}
	}

	public function postEdit($page_id, $version_number)
	{
		try
		{
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);
			$this->pageRepository->saveDraftVersion($version, Input::all());

			// Lets just redirect vack for the moment
			return Redirect::to(Coanda::adminUrl('pages/edit/' . $page_id . '/' . $version_number))->with('page_saved', true);
		}
		catch(ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages/edit/' . $page_id . '/' . $version_number))->with('error', true)->with('invalid_attributes', $exception->getInvalidFields());
		}
	}
}