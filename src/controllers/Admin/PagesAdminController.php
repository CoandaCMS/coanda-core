<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session;
use CoandaCMS\Coanda\Pages\PageManager;
use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;
use CoandaCMS\Coanda\Pages\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Pages\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException;
use CoandaCMS\Coanda\Pages\Exceptions\HomePageAlreadyExists;
use CoandaCMS\Coanda\Pages\Exceptions\SubPagesNotAllowed;
use CoandaCMS\Coanda\Controllers\BaseController;

class PagesAdminController extends BaseController {

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \CoandaCMS\Coanda\Pages\PageManager
     */
    private $manager;

    /**
     * @param PageManager $manager
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(PageManager $manager, PageRepositoryInterface $pageRepository)
	{
		$this->manager = $manager;
//		$this->pageRepository = $pageRepository;

		$this->beforeFilter('csrf', array('on' => 'post'));
	}

    /**
     * @param $view
     * @param array $data
     */
    private function __checkPermissions($view, $data = [])
	{
		Coanda::checkAccess('pages', $view, $data);
	}

    /**
     * @param $page
     * @return bool
     */
    private function __checkPagePermission($page)
	{
		// Is it the home page?
		if ($page->is_home && Coanda::canView('pages', 'home_page'))
		{
			return true;
		}

        return $page->can_view;
	}

    /**
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PermissionDenied
     */
    public function getIndex()
	{
		$this->__checkPermissions('view');

		return View::make('coanda::admin.modules.pages.index', [
				'home_page' => $this->manager->getHomePage(),
				'pages' => $this->manager->getAdminSubPages(0, (int) Input::get('page', 1), 10)
			]);
	}

    /**
     * @return mixed
     */
    public function postIndex()
	{
		if (Input::has('delete_selected') && Input::get('delete_selected') == 'true' && count(Input::get('remove_page_list', [])) > 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/confirm-delete'))->with('remove_page_list', Input::get('remove_page_list'));
		}

		if (Input::get('update_order', false) == 'true')
		{
			$this->manager->updatePageOrders(Input::get('ordering'));

			return Redirect::to(Coanda::adminUrl('pages'))->with('ordering_updated', true);
		}

		return Redirect::to(Coanda::adminUrl('pages'));
	}

    /**
     * @param $id
     * @return mixed
     */
    public function getView($id)
	{
        if ($id == 0)
        {
            return Redirect::to(Coanda::adminUrl('pages'));
        }

		try
		{
			$page = $this->manager->getPage($id);
            $per_page = 10;

			$this->__checkPagePermission($page);

			return View::make('coanda::admin.modules.pages.view', [
				'page' => $page,
                'children' => $this->manager->getAdminSubPages($page->id, (int) Input::get('page', 1), $per_page),
                'versions' => $this->manager->getVersionsForPagePaginated($page->id, $per_page, (int) Input::get('versions_page', 1)),
				'history' => $this->manager->pageHistory($id, 5),
				'contributors' => $this->manager->pageContributors($id)				
			]);
		}
		catch (PageNotFound $exception)
		{
			App::abort('404');
		}
	}

	public function postView($id)
	{
		if (Input::has('delete_selected') && Input::get('delete_selected') == 'true' && count(Input::get('remove_page_list', [])) > 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/confirm-delete'))->with('remove_page_list', Input::get('remove_page_list'))->with('previous_page_id', $id);
		}

		if (Input::get('update_order', false) == 'true')
		{
			$this->manager->updatePageOrders(Input::get('ordering', []));

			return Redirect::to(Coanda::adminUrl('pages/view/' . $id))->with('ordering_updated', true);
		}

		return Redirect::to(Coanda::adminUrl('pages/view/' . $id));
	}

    /**
     * @return mixed
     */
    public function getConfirmDelete()
	{
		$previous_page_id = Session::get('previous_page_id', 0);

		if (!Session::has('remove_page_list') || count(Session::get('remove_page_list')) == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/view/' . $previous_page_id));
		}

		return View::make('coanda::admin.modules.pages.confirmdelete', [
				'pages' => $this->manager->getPages(Session::get('remove_page_list')),
				'previous_page_id' => $previous_page_id
			]);

	}

    /**
     * @return mixed
     */
    public function postConfirmDelete()
	{
		if (!Input::has('confirmed_remove_list') || count(Input::get('confirmed_remove_list')) == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/view/' . Input::get('previous_page_id', 0)));
		}

		$this->manager->deletePages(Input::get('confirmed_remove_list'), (Input::get('permanent_delete', false) == 'true'));

		return Redirect::to(Coanda::adminUrl('pages/view/' . Input::get('previous_page_id', 0)));
	}

    /**
     * @param $page_type
     * @param bool $parent_page_id
     * @return mixed
     */
    public function getCreate($page_type, $parent_page_id = false)
	{
		$this->__checkPermissions('create', ['page_type' => $page_type, 'parent_page_id' => $parent_page_id]);

		try
		{
			$page = $this->manager->createNewPage($page_type, $parent_page_id);

			return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page->id . '/1'));
		}
		catch (PageTypeNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
		catch (SubPagesNotAllowed $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));	
		}
	}

    /**
     * @param $page_type
     * @return mixed
     */
    public function getCreateHome($page_type)
	{
		Coanda::checkAccess('pages', 'create', ['page_type' => $page_type]);

		try
		{
			$page = $this->manager->createHomePage($page_type);

			// Redirect to edit (version 1 - which should be the only version, give this is the create method!)
			return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page->id . '/1'));
		}
		catch (PageTypeNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
		catch (HomePageAlreadyExists $exception)
		{
			dd('home page already exists');

			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

    /**
     * @param $page_id
     * @param bool $base_version_number
     * @return mixed
     */
    public function getEdit($page_id, $base_version_number = false)
	{
		try
		{
			$page = $this->manager->getPage($page_id);

			if (!$page->can_edit)
			{
				return App::abort('403');
			}

			$existing_drafts = $this->manager->draftsForUser($page_id);

			if ($existing_drafts->count() > 0)
			{
				return Redirect::to(Coanda::adminUrl('pages/existing-drafts/' . $page->id));
			}
			else
			{
				$new_version = $this->manager->createNewVersionForPage($page->id, $base_version_number);

				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page->id . '/' . $new_version));
			}
		}		
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

    /**
     * @param $page_id
     * @param $version_number
     * @return mixed
     */
    public function getEditversion($page_id, $version_number)
	{
		try
		{
			$version = $this->manager->getDraftVersionForPage($page_id, $version_number);

			if (!$version->page->can_edit)
			{
				App::abort('403');
			}

			$publish_handlers = Coanda::module('pages')->publishHandlers();
			$default_publish_handler = array_keys($publish_handlers)[0];

			return View::make('coanda::admin.modules.pages.edit', [
				'version' => $version,
				'invalid_fields' => Session::get('invalid_fields', []),
				'publish_handler_invalid_fields' => Session::get('publish_handler_invalid_fields', []),
				'publish_handlers' => $publish_handlers,
				'default_publish_handler' => $default_publish_handler,
				'layouts' => Coanda::module('layout')->layoutsByPageType($version->page->type),
				'old_attribute_input' => Input::old('attributes', [])
			]);
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

    /**
     * @param $page_id
     * @param $version_number
     * @return mixed
     */
    public function postEditversion($page_id, $version_number)
	{
		try
		{
			$version = $this->manager->getDraftVersionForPage($page_id, $version_number);

			if (!$version->page->can_edit)
			{
				App::abort('403');
			}
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
		catch (PageVersionNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
		}

		if (Input::has('discard'))
		{
			$parent_page_id = $version->page->parent_page_id;

			$this->manager->removeDraftVersion($page_id, $version_number);
			
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

		$invalid_fields = [];

		try
		{
			$this->manager->saveDraftVersion($page_id, $version_number, Input::all());
		}
		catch (ValidationException $exception)
		{
			$invalid_fields = $exception->getInvalidFields();
		}

		$redirect = Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->withInput();

		if (Input::get('choose_layout', false) == 'true')
		{
			return $redirect->with('layout_chosen', true);
		}

		if (Input::get('choose_template', false) == 'true')
		{
			return $redirect->with('template_chosen', true);
		}

		if (count($invalid_fields) > 0)
		{
			return $redirect
					->with('error', true)
					->with('invalid_fields', $invalid_fields);
		}

		if (Input::get('save', false) == 'true')
		{
			return $redirect->with('page_saved', true);
		}

		if (Input::get('save_exit', false) == 'true')
		{
			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
		}

		if (Input::get('publish', false) == 'true')
		{
			$publish_handler = Input::get('publish_handler', false);

			if (!$publish_handler || $publish_handler == '')
			{
				return $redirect
							->with('error', true)
							->with('missing_publish_handler', true);
			}
			else
			{
				try
				{
					$publish_handler_redirect = $this->manager->executePublishHandler($this->manager->getDraftVersionForPage($page_id, $version_number), $publish_handler, Input::all());

					if ($publish_handler_redirect)
					{
						return Redirect::to($publish_handler_redirect);
					}

					return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));					
				}
				catch (PublishHandlerException $exception)
				{
					return $redirect
							->with('error', true)
							->with('invalid_publish_handler', true)
							->with('publish_handler_invalid_fields', $exception->getInvalidFields());
				}
			}
		}
	}

    /**
     * @param $page_id
     * @param $version_number
     * @return mixed
     */
    public function getRemoveversion($page_id, $version_number)
	{
		try
		{
			$version = $this->manager->getDraftVersionForPage($page_id, $version_number);
			$page = $version->page;

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			$this->manager->removeDraftVersion($page_id, $version_number);

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

    /**
     * @param $page_id
     * @return mixed
     */
    public function getExistingDrafts($page_id)
	{
		try
		{
			$page = $this->manager->getPage($page_id);

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			$drafts = $this->manager->draftsForUser($page_id, Coanda::currentUser()->id);

			return View::make('coanda::admin.modules.pages.existingdrafts', ['page' => $page, 'drafts' => $drafts ]);
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

    /**
     * @param $page_id
     * @return mixed
     */
    public function postExistingDrafts($page_id)
	{
		try
		{
			$page = $this->pageRepository->find($page_id);

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			if (Input::has('new_version') && Input::get('new_version') == 'true')
			{
				$new_version = $this->pageRepository->createNewVersion($page->id, Coanda::currentUser()->id);

				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $new_version));
			}
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

    /**
     * @param $page_id
     * @return mixed
     */
    public function getDelete($page_id)
	{
		try
		{
			$page = $this->manager->getPage($page_id);

			Coanda::checkAccess('pages', 'remove', ['page_id' => $page->id, 'page_type' => $page->type]);

			return View::make('coanda::admin.modules.pages.delete', ['page' => $page ]);
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

    /**
     * @param $page_id
     * @return mixed
     */
    public function postDelete($page_id)
	{
		try
		{
			$page = $this->manager->getPage($page_id);
			$parent_page_id = $page->parent_page_id;

			Coanda::checkAccess('pages', 'remove', ['page_id' => $page->id, 'page_type' => $page->type]);

			$permanent = false;

			if (Input::has('permanent_delete') && Input::get('permanent_delete') == 'true')
			{
				$permanent = true;
			}

			$this->manager->deletePage($page_id, $permanent);

			if ($permanent)
			{
				return Redirect::to(Coanda::adminUrl('pages/view/' . $parent_page_id));
			}

			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

    /**
     * @return mixed
     */
    public function getTrash()
	{
		Coanda::checkAccess('pages', 'remove');

		return View::make('coanda::admin.modules.pages.trash', ['pages' => $this->manager->getTrashedPages() ]);
	}

    /**
     * @return mixed
     */
    public function postTrash()
	{
		Coanda::checkAccess('pages', 'remove');

		if (!Input::has('permanent_remove_list') || count(Input::get('permanent_remove_list')) == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/trash'));
		}

		return Redirect::to(Coanda::adminUrl('pages/confirm-delete-from-trash'))->with('confirm_permanent_remove_list', Input::get('permanent_remove_list'));
	}

    /**
     * @return mixed
     */
    public function getConfirmDeleteFromTrash()
	{
		Coanda::checkAccess('pages', 'remove');

		if (!Session::has('confirm_permanent_remove_list') || count(Session::get('confirm_permanent_remove_list')) == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/trash'));
		}

		$pages = $this->manager->getPages(Session::get('confirm_permanent_remove_list'));

		return View::make('coanda::admin.modules.pages.confirmdeletefromtrash', ['pages' => $pages ]);
	}

    /**
     * @return mixed
     */
    public function postConfirmDeleteFromTrash()
	{
		Coanda::checkAccess('pages', 'remove');

		if (!Input::has('confirmed_remove_list') || count(Input::get('confirmed_remove_list')) == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/trash'));
		}

		$this->manager->deletePages(Input::get('confirmed_remove_list'), true);

		return Redirect::to(Coanda::adminUrl('pages/trash'));
	}

    /**
     * @param $page_id
     * @return mixed
     */
    public function getRestore($page_id)
	{
		try
		{
			$page = $this->manager->getPage($page_id);

			Coanda::checkAccess('pages', 'remove');

			if (!$page->is_trashed)
			{
				return Redirect::to(Coanda::adminUrl('pages/view/' . $page->id));
			}

			return View::make('coanda::admin.modules.pages.restore', ['page' => $page ]);
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

    /**
     * @param $page_id
     * @return mixed
     */
    public function postRestore($page_id)
	{
		try
		{
			Coanda::checkAccess('pages', 'remove');

			$this->manager->restorePage($page_id, Input::has('restore_sub_pages') ? Input::get('restore_sub_pages') : []);

			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
		}
		catch (PageNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
	}

    /**
     * @param $id
     * @internal param $page_id
     * @return mixed
     */
    public function getHistory($id)
    {
        try
        {
            $page = $this->manager->getPage($id);

			$this->__checkPermissions('view', ['page_id' => $page->id, 'page_type' => $page->type]);

            return View::make('coanda::admin.modules.pages.history', [
            						'page' => $page,
            						'histories' => $this->manager->pageHistory($page->id),
            						'contributors' => $this->manager->pageContributors($page->id)
            					]);
        }
        catch (PageNotFound $exception)
        {
            return Redirect::to(Coanda::adminUrl('pages'));
        }
    }

    /**
     * @param $page_id
     * @param $new_sub_page_order
     * @return mixed
     */
    public function getChangePageOrder($page_id, $new_sub_page_order)
    {
 		try
		{
			$page = $this->manager->getPage($page_id);

			Coanda::checkAccess('pages', 'view', ['page_id' => $page->id, 'page_type' => $page->type]);

			$this->manager->updateSubPageOrder($page->id, $new_sub_page_order);

			return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
		}
		catch (PageNotFound $exception)
		{
			App::abort('404');
		}
    }

    public function getPageListJson($page_id = false)
    {
		$page = false;

		if ($page_id)
		{
			$page = $this->manager->getPage($page_id);
		}

		$per_page = 10;
		$sub_pages = $this->manager->getAdminSubPages($page_id, Input::get('page', 1), $per_page);

		return [
				'page' => $page ? $page->toArray() : false,
				'sub_pages' => $sub_pages->toArray()
			];
    }
}