<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session;

use CoandaCMS\Coanda\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Exceptions\PermissionDenied;

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
     * @var \CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @param CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository
     */
    public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository)
	{
		$this->pageRepository = $pageRepository;

		$this->beforeFilter('csrf', array('on' => 'post'));
	}

    /**
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PermissionDenied
     */
    public function getIndex()
	{
		Coanda::checkAccess('pages', 'view');

		$home_page = $this->pageRepository->getHomePage();

		$per_page = 10;

		$pages = $this->pageRepository->topLevel($per_page);

		return View::make('coanda::admin.modules.pages.index', [ 'home_page' => $home_page, 'pages' => $pages ]);
	}

    /**
     * @return mixed
     */
    public function postIndex()
	{
		// Did we hit the delete button?
		if (Input::has('delete_selected') && Input::get('delete_selected') == 'true')
		{
			if (!Input::has('remove_page_list') || count(Input::get('remove_page_list')) == 0)
			{
				return Redirect::to(Coanda::adminUrl('pages'));
			}

			return Redirect::to(Coanda::adminUrl('pages/confirm-delete'))->with('remove_page_list', Input::get('remove_page_list'));
		}

		if (Input::has('update_order') && Input::get('update_order') == 'true')
		{
			$this->pageRepository->updateOrdering(Input::get('ordering'));

			return Redirect::to(Coanda::adminUrl('pages'))->with('ordering_updated', true);
		}
	}

    /**
     * @param $id
     * @return mixed
     */
    public function getView($id)
	{
		try
		{
			$page = $this->pageRepository->find($id);

			// If the page is only in one place, lets redirect and view it there
			if ($page->locations->count() == 1)
			{
				return Redirect::to(Coanda::adminUrl('pages/location/' . $page->locations()->first()->id));
			}

			$view_data = [
				'page' => $page,
				'history' => $this->pageRepository->recentHistory($id, 5),
				'contributors' => $this->pageRepository->contributors($id)				
			];

			return View::make('coanda::admin.modules.pages.view', $view_data);
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
			$pagelocation = $this->pageRepository->locationById($id);

			Coanda::checkAccess('pages', 'view', ['page_location_id' => $pagelocation->id, 'page_location_path' => $pagelocation->path, 'page_type' => $pagelocation->page->type]);

			$view_data = [
							'pagelocation' => $pagelocation,
							'page' => $pagelocation->page,
							'children' => $this->pageRepository->subPages($id, 10),
							'history' => $this->pageRepository->recentHistory($pagelocation->page->id, 5),
							'contributors' => $this->pageRepository->contributors($pagelocation->page->id)
						];

			return View::make('coanda::admin.modules.pages.location', $view_data);
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
		if ($id == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages'));
		}

		// Did we hit the delete button?
		if (Input::has('delete_selected') && Input::get('delete_selected') == 'true')
		{
			if (!Input::has('remove_page_list') || count(Input::get('remove_page_list')) == 0)
			{
				return Redirect::to(Coanda::adminUrl('pages/location/' . $id));
			}

			return Redirect::to(Coanda::adminUrl('pages/confirm-delete'))->with('remove_page_list', Input::get('remove_page_list'))->with('previous_location_id', $id);
		}

		if (Input::has('update_order') && Input::get('update_order') == 'true')
		{
			$this->pageRepository->updateOrdering(Input::get('ordering'));

			return Redirect::to(Coanda::adminUrl('pages/location/' . $id))->with('ordering_updated', true);
		}
	}

    /**
     * @return mixed
     */
    public function getConfirmDelete()
	{
		$previous_location_id = Session::get('previous_location_id', 0);

		if (!Session::has('remove_page_list') || count(Session::get('remove_page_list')) == 0)
		{
			if (!$previous_location_id)
			{
				return Redirect::to(Coanda::adminUrl('pages'));
			}

			return Redirect::to(Coanda::adminUrl('pages/location/' . $previous_location_id));
		}

		$pages = $this->pageRepository->findByIds(Session::get('remove_page_list'));

		return View::make('coanda::admin.modules.pages.confirmdelete', ['pages' => $pages, 'previous_location_id' => $previous_location_id]);

	}

    /**
     * @return mixed
     */
    public function postConfirmDelete()
	{
		$previous_page_id = Input::get('previous_page_id');

		if (!$previous_page_id)
		{
			$previous_page_id = 0;
		}

		if (!Input::has('confirmed_remove_list') || count(Input::get('confirmed_remove_list')) == 0)
		{
			return Redirect::to(Coanda::adminUrl('pages/location/' . $previous_page_id));
		}

		$this->pageRepository->deletePages(Input::get('confirmed_remove_list'));

		return Redirect::to(Coanda::adminUrl('pages/location/' . $previous_page_id));
	}

    /**
     * @param $page_type
     * @param bool $parent_page_id
     * @return mixed
     */
    public function getCreate($page_type, $parent_page_id = false)
	{
		Coanda::checkAccess('pages', 'create', ['page_type' => $page_type, 'parent_page_id' => $parent_page_id]);

		try
		{
			$type = Coanda::module('pages')->getPageType($page_type);
			$page = $this->pageRepository->create($type, Coanda::currentUser()->id, $parent_page_id);

			// Redirect to edit (version 1 - which should be the only version, given this is the create method!)
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
     * @return mixed
     */
    public function getEdit($page_id, $version_number = false)
	{
		try
		{
			$page = $this->pageRepository->find($page_id);

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			$existing_drafts = $this->pageRepository->draftsForUser($page_id, Coanda::currentUser()->id);	

			if ($existing_drafts->count() > 0)
			{
				return Redirect::to(Coanda::adminUrl('pages/existing-drafts/' . $page->id));
			}
			else
			{
				$new_version = $this->pageRepository->createNewVersion($page_id, Coanda::currentUser()->id, $version_number);

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
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);

			$page = $version->page;

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			$publish_handlers = Coanda::module('pages')->publishHandlers();
			$publish_handler_invalid_fields = Session::has('publish_handler_invalid_fields') ? Session::get('publish_handler_invalid_fields') : [];
			$default_publish_handler = array_keys($publish_handlers)[0];

			$layouts = Coanda::module('layout')->layoutsByPageType($page->type);

			return View::make('coanda::admin.modules.pages.edit', ['version' => $version, 'invalid_fields' => $invalid_fields, 'publish_handler_invalid_fields' => $publish_handler_invalid_fields, 'publish_handlers' => $publish_handlers, 'default_publish_handler' => $default_publish_handler, 'layouts' => $layouts ]);
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
			$version = $this->pageRepository->getDraftVersion($page_id, $version_number);
			$page = $version->page;

			Coanda::checkAccess('pages', 'edit', ['page_id' => $page->id, 'page_type' => $page->type]);

			if (Input::has('discard'))
			{
				$this->pageRepository->discardDraftVersion($version);

				// If this was the first version, then we need to redirect back to the parent
				if ($version_number == 1)
				{					
					return Redirect::to(Coanda::adminUrl('pages'));
				}
				else
				{
					return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
				}
			}

			$this->pageRepository->saveDraftVersion($version, Input::all());

			// Everything went OK, so now we can determine what to do based on the button
			if (Input::has('choose_layout') && Input::get('choose_layout') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('layout_chosen', true)->withInput();
			}

			if (Input::has('update_region_block_order') && Input::get('update_region_block_order') == 'true')
			{
				Coanda::module('layout')->updateCustomRegionBlockOrders(Input::get('region_block_ordering'));

				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('ordering_updated', true)->withInput();
			}

			if (Input::has('add_custom_block'))
			{
				return Redirect::to(Coanda::adminUrl('layout/page-custom-region-block/' . $page_id . '/' . $version_number . '/' . Input::get('add_custom_block')));
			}

			if (Input::has('add_location'))
			{
				return Redirect::to(Coanda::adminUrl('pages/browse-add-location/' . $page_id . '/' . $version_number));
			}

			if (Input::has('remove_locations'))
			{
				$slug_ids = Input::has('remove_slug_list') ? Input::get('remove_slug_list') : [];
				
				foreach ($slug_ids as $slug_id)
				{
					$this->pageRepository->removeVersionSlug($version->id, $slug_id);
				}

				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->withInput();
			}

			if (Input::has('attribute_action'))
			{
				$this->pageRepository->handleAttributeAction($version, Input::get('attribute_action'), Input::all());

				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->withInput();
			}

			if (Input::has('save') && Input::get('save') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('page_saved', true)->withInput();
			}

			if (Input::has('save_exit') && Input::get('save_exit') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
			}

			if (Input::has('publish') && Input::get('publish') == 'true')
			{
				if (!Input::has('publish_handler') || Input::get('publish_handler') == '')
				{
					return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('error', true)->with('missing_publish_handler', true)->withInput();
				}
				else
				{
					try
					{
						$redirect = $this->pageRepository->executePublishHandler($version, Input::get('publish_handler'), Input::all());

						if ($redirect)
						{
							return Redirect::to($redirect);
						}

						return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));					
					}
					catch (PublishHandlerException $exception)
					{
						return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('error', true)->with('invalid_publish_handler', true)->with('publish_handler_invalid_fields', $exception->getInvalidFields())->withInput();
					}
				}
			}
		}
		catch (ValidationException $exception)
		{
			if (Input::has('attribute_action'))
			{
				$this->pageRepository->handleAttributeAction($version, Input::get('attribute_action'), Input::all());

				return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->withInput();
			}

			if (Input::has('add_location'))
			{
				return Redirect::to(Coanda::adminUrl('pages/browse-add-location/' . $page_id . '/' . $version_number));
			}

			if (Input::has('remove_locations'))
			{
				$slug_ids = Input::has('remove_slug_list') ? Input::get('remove_slug_list') : [];

				foreach ($slug_ids as $slug_id)
				{
					$this->pageRepository->removeVersionSlug($version->id, $slug_id);
				}
			}

			if (Input::has('add_custom_block'))
			{
				return Redirect::to(Coanda::adminUrl('layout/page-custom-region-block/' . $page_id . '/' . $version_number . '/' . Input::get('add_custom_block')));
			}

			if (Input::has('save_exit') && Input::get('save_exit') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('pages/view/' . $page_id));
			}

			return Redirect::to(Coanda::adminUrl('pages/editversion/' . $page_id . '/' . $version_number))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
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
			
			$pages = $this->pageRepository->subPages($parent_page_id, $per_page);

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

			Coanda::checkAccess('pages', 'remove', ['page_id' => $page->id, 'page_type' => $page->type]);

			$this->pageRepository->deletePage($page_id);

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
     * @param $page_id
     * @return mixed
     */
    public function getHistory($page_id)
    {
        try
        {
            $page = $this->pageRepository->find($page_id);
 
			Coanda::checkAccess('pages', 'view', ['page_id' => $page->id, 'page_type' => $page->type]);

            $history = $this->pageRepository->history($page->id);
            $contributors = $this->pageRepository->contributors($page->id);

            return View::make('coanda::admin.modules.pages.history', [ 'page' => $page, 'histories' => $history, 'contributors' => $contributors]);
        }
        catch (PageNotFound $exception)
        {
            return Redirect::to(Coanda::adminUrl('pages'));
        }
    }

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

    public function getIndexLocationTest($location_id)
    {
		$pagelocation = $this->pageRepository->locationById($location_id);

		$this->pageRepository->registerLocationWithSearchProvider($pagelocation);
    }
}