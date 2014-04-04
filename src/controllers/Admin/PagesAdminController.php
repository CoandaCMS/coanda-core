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

	public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository)
	{
		$this->pageRepository = $pageRepository;

		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		if (!Coanda::canAccess('pages'))
		{
			throw new PermissionDenied;
		}

		$pages = $this->pageRepository->topLevel();

		return View::make('coanda::admin.pages.index', [ 'pages' => $pages ]);
	}

	public function getView($id)
	{
		if (!Coanda::canAccess('pages'))
		{
			throw new PermissionDenied;
		}

		if ($id == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}

		try
		{
			$page = $this->pageRepository->find($id);
			$history = $this->pageRepository->history($page->id);

			return View::make('coanda::admin.pages.view', ['page' => $page, 'history' => $history]);
		}
		catch(PageNotFound $exception)
		{
			App::abort('404');
		}
	}

	public function getCreate($page_type, $parent_page_id = false)
	{
		try
		{
			$type = Coanda::getPageType($page_type);
			$page = $this->pageRepository->create($type, Coanda::currentUser()->id, $parent_page_id);

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
		try
		{
			$existing_drafts = $this->pageRepository->draftsForUser($page_id, Coanda::currentUser()->id);	

			if ($existing_drafts->count() > 0)
			{
				return Redirect::to(Coanda::adminUrl('pages/existing-drafts/' . $page_id));
			}
			else
			{
				$new_version = $this->pageRepository->createNewVersion($page_id, Coanda::currentUser()->id);

				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $new_version));
			}
		}		
		catch(PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
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
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
		catch (PageVersionNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
		}
	}

	public function postEditversion($page_id, $version_number)
	{
		try
		{
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);

			if (Input::has('discard'))
			{
				$parent_page_id = $version->page->parent_page_id;

				$this->pageRepository->discardDraftVersion($version);

				// If this was the first version, then we need to redirect back to the parent
				if ($version_number == 1)
				{
					return Redirect::to(Coanda::adminUrl('pages/view/' . $parent_page_id));
				}
				else
				{
					return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
				}
			}

			$this->pageRepository->saveDraftVersion($version, Input::all());

			// Everything went OK, so now we can determine what to do based on the button
			if (Input::has('save') && Input::get('save') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('page_saved', true);
			}

			if (Input::has('save_exit') && Input::get('save_exit') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
			}

			if (Input::has('publish') && Input::get('publish') == 'true')
			{
				$this->pageRepository->publishVersion($version);

				return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
			}
		}
		catch (ValidationException $exception)
		{
			if (Input::has('save_exit') && Input::get('save_exit') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
			}

			return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}

	public function getRemoveversion($page_id, $version_number)
	{
		try
		{
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);

			$this->pageRepository->discardDraftVersion($version);

			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));	
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
		catch (PageVersionNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
		}
	}

	public function getExistingDrafts($page_id)
	{
		try
		{
			$page = $this->pageRepository->find($page_id);
			$drafts = $this->pageRepository->draftsForUser($page_id, Coanda::currentUser()->id);

			return View::make('coanda::admin.pages.existingdrafts', ['page' => $page, 'drafts' => $drafts ]);
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

	public function postExistingDrafts($page_id)
	{
		try
		{
			if (Input::has('new_version') && Input::get('new_version') == 'true')
			{
				$page = $this->pageRepository->find($page_id);

				$new_version = $this->pageRepository->createNewVersion($page->id, Coanda::currentUser()->id);

				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $new_version));
			}
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

	public function getDelete($page_id)
	{
		try
		{
			$page = $this->pageRepository->find($page_id);

			return View::make('coanda::admin.pages.delete', ['page' => $page ]);
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

	public function postDelete($page_id)
	{
		try
		{
			$page = $this->pageRepository->find($page_id);
			$parent_page_id = $page->parent_page_id;

			$this->pageRepository->deletePage($page_id);

			return Redirect::to(Coanda::adminUrl('pages/view/' . $parent_page_id));
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

	public function getTrash()
	{
		$pages = $this->pageRepository->trashed();

		return View::make('coanda::admin.pages.trash', ['pages' => $pages ]);
	}

	public function getRestore($page_id)
	{
		try
		{
			// Get the page to be restored
			$page = $this->pageRepository->find($page_id);

			if (!$page->is_trashed)
			{
				return Redirect::to(Coanda::adminUrl('pages/view/' . $page->id));
			}

			// Get all the parent pages which would have to be restored too
			$trashed_parents = $this->pageRepository->trashedParentsForPage($page->id);

			return View::make('coanda::admin.pages.restore', ['page' => $page, 'trashed_parents' => $trashed_parents ]);
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

	public function postRestore($page_id)
	{
		try
		{
			$restore_sub_pages = Input::has('restore_sub_pages') && Input::get('restore_sub_pages') == 'yes';

			$this->pageRepository->restore($page_id, $restore_sub_pages);

			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}
}