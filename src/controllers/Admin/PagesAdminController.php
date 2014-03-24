<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session;

use CoandaCMS\Coanda\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Exceptions\PermissionDenied;

use CoandaCMS\Coanda\Controllers\BaseController;

class PagesAdminController extends BaseController {

	private $pageRepository;
	private $pagePresenter;

	public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository, \CoandaCMS\Coanda\Pages\Presenters\PagePresenter $pagePresenter)
	{
		$this->pageRepository = $pageRepository;
		$this->pagePresenter = $pagePresenter;

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
			$this->pagePresenter->setModel($page);

			return View::make('coanda::admin.pages.view', ['page' => $this->pagePresenter]);
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
			return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page->id . '/1'));
		}
		catch (PageTypeNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}

	}

	public function getEdit($page_id)
	{
		// create a new version of the page and redirect to edit the version
		try
		{
			$new_version = $this->pageRepository->createNewVersion($page_id, Coanda::currentUser()->id);

			return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $new_version));			
		}
		catch(PermissionDenied $exception)
		{
			dd('permission denied');
		}
	}

	public function getEditversion($page_id, $version_number)
	{
		try
		{
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);
			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			return View::make('coanda::admin.pages.edit', ['version' => $version, 'invalid_fields' => $invalid_fields ]);
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

	public function postEditversion($page_id, $version_number)
	{
		try
		{
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);
			$this->pageRepository->saveDraftVersion($version, Input::all());

			// Everything went OK, so now we can determine what to do based on the button
			if (Input::has('save') && Input::get('save') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('page_saved', true);			
			}

			if (Input::has('publish') && Input::get('publish') == 'true')
			{
				try
				{
					$this->pageRepository->publishVersion($version);

					return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
				}
				catch(Exception $exception)
				{
					dd('huh?');
				}
			}
		}
		catch(ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}
}