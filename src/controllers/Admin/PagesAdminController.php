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

/**
 * Class PagesAdminController
 * @package CoandaCMS\Coanda\Controllers\Admin
 */
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
		$this->pageRepository = $pageRepository;

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

		// Can we view this page in any of its locations?
		foreach ($page->locations as $location)
		{
			if ($location->can_view)
			{
				return true;
			}
		}

		App::abort('404');
	}

    /**
     * @param $location
     */
    private function __checkLocationPermission($location)
	{
		if (!$location->can_view)
		{
			App::abort('403');
		}
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
				'pages' => $this->manager->getAdminSubLocations(0, (int) Input::get('page', 1), 10)
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
			$this->manager->updateLocationOrders(Input::get('ordering'));

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
		try
		{
			$page = $this->manager->getPage($id);

			// If the page is only in one place, lets redirect and view it there
			if ($page->locations->count() == 1)
			{
				return Redirect::to(Coanda::adminUrl('pages/location/' . $page->locations()->first()->id));
			}

			$this->__checkPagePermission($page);

			return View::make('coanda::admin.modules.pages.view', [
				'page' => $page,
				'history' => $this->manager->pageHistory($id, 5),
				'contributors' => $this->manager->pageContributors($id)				
			]);
		}
		catch (PageNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @param $id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PermissionDenied
     */
    public function getLocation($id)
	{
		if ($id == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}

		try
		{
			$location = $this->manager->getLocation($id);

			$this->__checkLocationPermission($location);

			return View::make('coanda::admin.modules.pages.location', [
					'pagelocation' => $location,
					'page' => $location->page,
					'children' => $this->manager->getAdminSubLocations($location->id, (int) Input::get('page', 1), 10),
					'history' => $this->manager->pageHistory($location->page->id, 5),
					'contributors' => $this->manager->pageContributors($location->page->id)
			]);
		}
		catch (PageNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @param $id
     * @return mixed
     */
    public function postLocation($id)
	{
		if (Input::has('delete_selected') && Input::get('delete_selected') == 'true' && count(Input::get('remove_page_list', [])) > 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/confirm-delete'))->with('remove_page_list', Input::get('remove_page_list'))->with('previous_location_id', $id);
		}

		if (Input::get('update_order', false) == 'true')
		{
			$this->manager->updateLocationOrders(Input::get('ordering', []));

			return Redirect::to(Coanda::adminUrl('pages/location/' . $id))->with('ordering_updated', true);
		}

		return Redirect::to(Coanda::adminUrl('pages/location/' . $id));
	}

    /**
     * @return mixed
     */
    public function getConfirmDelete()
	{
		$redirect_location_id = Session::get('previous_location_id', 0);

		if (!Session::has('remove_page_list') || count(Session::get('remove_page_list')) == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/location/' . $redirect_location_id));
		}

		return View::make('coanda::admin.modules.pages.confirmdelete', [
				'pages' => $this->manager->getPages(Session::get('remove_page_list')),
				'previous_location_id' => $redirect_location_id
			]);

	}

    /**
     * @return mixed
     */
    public function postConfirmDelete()
	{
		if (!Input::has('confirmed_remove_list') || count(Input::get('confirmed_remove_list')) == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/location/' . Input::get('previous_location_id', 0)));
		}

		$this->manager->deletePages(Input::get('confirmed_remove_list'), (Input::get('permanent_delete', false) == 'true'));

		return Redirect::to(Coanda::adminUrl('pages/location/' . Input::get('previous_location_id', 0)));
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
			$type = Coanda::module('pages')->getHomePageType($page_type);
			$page = $this->pageRepository->createHome($type, Coanda::currentUser()->id);

			// Redirect to edit (version 1 - which should be the only version, give this is the create method!)
			return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page->id . '/1'));
		}
		catch (PageTypeNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}
		catch (HomePageAlreadyExists $exception)
		{
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
			$version = $this->manager->getVersionForPage($page_id, $version_number);

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
			$version = $this->manager->getVersionForPage($page_id, $version_number);

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
			$parent_page_id = $version->page->firstLocation()->parent_page_id;

			$this->manager->removeDraftVersion($page_id, $version_number);
			
			// If this was the first version, then we need to redirect back to the parent
			if ($version_number == 1)
			{
				return Redirect::to(Coanda::adminUrl('pages/location/' . $parent_page_id));	
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

		if (Input::has('add_location'))
		{
			return Redirect::to(Coanda::adminUrl('pages/browse-add-location/' . $page_id . '/' . $version_number));
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

		if (Input::has('remove_locations'))
		{
			foreach (Input::get('remove_slug_list', []) as $slug_id)
			{
				$this->manager->removeSlugFromVersion($version->id, $slug_id);
			}

			return $redirect;
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
					$publish_handler_redirect = $this->pageRepository->executePublishHandler($version, $publish_handler, Input::all());

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
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);
			$page = $version->page;

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			$this->pageRepository->discardDraftVersion($version, Coanda::currentUser()->id);

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
     * @param $version_number
     * @param int $parent_page_id
     * @return mixed
     */
    public function getBrowseAddLocation($page_id, $version_number, $parent_page_id = 0)
	{
		try
		{
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);
			$page = $version->page;

			$existing_locations = [];

			foreach ($version->slugs as $version_slug)
			{
				$existing_locations[] = $version_slug->page_location_id;
			}

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			$per_page = 10;

			$location = false;

			if ($parent_page_id !== 0)
			{
				$location = $this->pageRepository->locationById($parent_page_id);
			}
			
			$pages = $this->pageRepository->subPages($parent_page_id, $per_page, ['include_hidden' => true, 'include_drafts' => true, 'include_invisible' => true]);

			return View::make('coanda::admin.modules.pages.browseaddlocation', [ 'pages' => $pages, 'page_id' => $page_id, 'version_number' => $version_number, 'existing_locations' => $existing_locations, 'location' => $location ]);
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
    public function postAddLocation($page_id, $version_number)
	{
		try
		{
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);
			$page = $version->page;

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			if (Input::has('add_locations') && count(Input::get('add_locations')) > 0)
			{
				foreach (Input::get('add_locations') as $new_location_id)
				{
					$this->pageRepository->addNewVersionSlug($version->id, $new_location_id);
				}
			}

			return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number));
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
			$page = $this->pageRepository->find($page_id);

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			$drafts = $this->pageRepository->draftsForUser($page_id, Coanda::currentUser()->id);

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
			$page = $this->pageRepository->find($page_id);

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
			$page = $this->pageRepository->find($page_id);

			$firstLocation = $page->firstLocation();
			$parent_page_id = 0;

			if ($firstLocation)
			{
				$parent_page_id = $firstLocation->parent_page_id;				
			}

			Coanda::checkAccess('pages', 'remove', ['page_id' => $page->id, 'page_type' => $page->type]);

			$permanent = false;

			if (Input::has('permanent_delete') && Input::get('permanent_delete') == 'true')
			{
				$permanent = true;
			}

			$this->pageRepository->deletePage($page_id, $permanent);

			if ($permanent)
			{
				return Redirect::to(Coanda::adminUrl('pages/location/' . $parent_page_id));
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

		$pages = $this->pageRepository->trashed();

		return View::make('coanda::admin.modules.pages.trash', ['pages' => $pages ]);
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

		$pages = $this->pageRepository->findByIds(Session::get('confirm_permanent_remove_list'));

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

		$this->pageRepository->deletePages(Input::get('confirmed_remove_list'), true);

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
			// Get the page to be restored
			$page = $this->pageRepository->find($page_id);

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

			$this->pageRepository->restore($page_id, Input::has('restore_sub_pages') ? Input::get('restore_sub_pages') : []);

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
     * @param $location_id
     * @param $new_sub_page_order
     * @return mixed
     */
    public function getChangeLocationOrder($location_id, $new_sub_page_order)
    {
 		try
		{
			$pagelocation = $this->pageRepository->locationById($location_id);

			Coanda::checkAccess('pages', 'view', ['page_location_id' => $pagelocation->id, 'page_type' => $pagelocation->page->type]);

			$this->pageRepository->updateLocationSubPageOrder($pagelocation->id, $new_sub_page_order);

			return Redirect::to(Coanda::adminUrl('pages/location/' . $location_id));
		}
		catch (PageNotFound $exception)
		{
			App::abort('404');
		}
    }

    /**
     * @param $location_id
     */
    public function getIndexLocationTest($location_id)
    {
		$pagelocation = $this->pageRepository->locationById($location_id);

		$this->pageRepository->registerLocationWithSearchProvider($pagelocation);
    }

    /**
     * @param bool $location_id
     * @return array
     */
    public function getLocationListJson($location_id = false)
    {
		$location = false;

		if ($location_id)
		{
			$location = $this->pageRepository->locationById($location_id);
		}

		$per_page = 10;

		return [
				'location' => $location ? $location->toArray() : false,
				'sub_pages' => $this->pageRepository->subPages($location_id, $per_page, ['include_hidden' => true, 'include_drafts' => true, 'include_invisible' => true])->toArray()
			];
    }
}