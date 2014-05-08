<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent;

use Coanda;

use CoandaCMS\Coanda\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;
use CoandaCMS\Coanda\Exceptions\ValidationException;

use CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException;
use CoandaCMS\Coanda\Pages\Exceptions\HomePageAlreadyExists;
use CoandaCMS\Coanda\Pages\Exceptions\SubPagesNotAllowed;

use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageLocation as PageLocationModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page as PageModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion as PageVersionModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersionSlug as PageVersionSlugModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute as PageAttributeModel;

use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;

use Carbon\Carbon;

/**
 * Class EloquentPageRepository
 * @package CoandaCMS\Coanda\Pages\Repositories\Eloquent
 */
class EloquentPageRepository implements PageRepositoryInterface {

    /**
     * @var Models\Page
     */
    private $model;
    private $page_location_model;
    private $page_version_slug_model;

    /**
     * @var \CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface
     */
    private $urlRepository;
    /**
     * @var \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @param PageModel $model
     * @param CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface $urlRepository
     * @param CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface $historyRepository
     */
    public function __construct(PageLocationModel $page_location_model, PageModel $model, PageVersionSlugModel $page_version_slug_model, \CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface $urlRepository, \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface $historyRepository)
	{
		$this->page_location_model = $page_location_model;
		$this->page_version_slug_model = $page_version_slug_model;
		$this->model = $model;
		$this->urlRepository = $urlRepository;
		$this->historyRepository = $historyRepository;
	}

    /**
     * @param $id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageNotFound
     */
    public function find($id)
	{
		$page = $this->model->find($id);

		if (!$page)
		{
			throw new PageNotFound('Page #' . $id . ' not found');
		}
		
		return $page;
	}

    /**
     * @param $id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageNotFound
     */
    public function findById($id)
	{
		$page = $this->model->find($id);

		if (!$page)
		{
			throw new PageNotFound('Page #' . $id . ' not found');
		}
		
		return $page;
	}

	public function locationById($id)
	{
		$location = $this->page_location_model->find($id);

		if (!$location)
		{
			throw new PageNotFound('Page Location #' . $id . ' not found');
		}

		return $location;
	}

    /**
     * @param $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByIds($ids)
	{
		$pages = new \Illuminate\Database\Eloquent\Collection;

		if (!is_array($ids))
		{
			return $pages;
		}

		foreach ($ids as $id)
		{
			$page = $this->model->find($id);

			if ($page)
			{
				$pages->add($page);
			}
		}

		return $pages;
	}

	private function subLocationsForLocation($parent_location_id, $per_page = 10)
	{
		return $this->page_location_model->where('parent_page_id', $parent_location_id)->whereHas('page', function ($query) { $query->where('is_trashed', '=', '0'); })->orderBy('order', 'asc')->paginate($per_page);
	}

    /**
     * @param int $per_page
     * @return mixed
     */
    public function topLevel($per_page = 10)
	{
		return $this->subLocationsForLocation(0, $per_page);

		// return $this->model->where('parent_page_id', 0)->whereIsHome(false)->whereIsTrashed(false)->orderBy('order', 'asc')->paginate($per_page);
	}

    /**
     * @param $page_id
     * @param $per_page
     * @return mixed
     */
    public function subPages($location_id, $per_page)
	{
		return $this->subLocationsForLocation($location_id, $per_page);

		// return $this->page_location_model->where('parent_page_id', $page_id)->orderBy('order', 'asc')->paginate($per_page);
	}

	private function createNewPage($type, $is_home, $user_id, $parent_pagelocation_id = false)
	{
		// create a page model
		$page = new PageModel;

		if ($is_home)
		{
			$page->is_home = true;
		}

		$page->type = $type->identifier();
		$page->created_by = $user_id;
		$page->edited_by = $user_id;
		$page->current_version = 1;

		$page->save();

		// Create the version
		$version = new PageVersionModel;
		$version->version = 1;
		$version->status = 'draft';
		$version->created_by = $user_id;
		$version->edited_by = $user_id;

		$page->versions()->save($version);

		// Add all the attributes..
		$index = 1;

		foreach ($type->attributes() as $type_attribute)
		{
			$page_attribute_type = Coanda::getAttributeType($type_attribute['type']);

			$attribute = new PageAttributeModel;

			$attribute->type = $page_attribute_type->identifier();
			$attribute->identifier = $type_attribute['identifier'];
			$attribute->order = $index;

			$version->attributes()->save($attribute);

			$index ++;
		}

		// If we are dealing with the home page, then we don't need to add a location
		if (!$is_home)
		{
			$location = new $this->page_location_model;
			$location->page_id = $page->id;

			// Work out the parent and path fields...
			if ($parent_pagelocation_id)
			{
				$parent_location = $this->page_location_model->find($parent_pagelocation_id);

				if ($parent_location)
				{
					$location->parent_page_id = $parent_location->id;

					$path = $parent_location->path == '' ? '/' : $parent_location->path;

					$location->path = $path . $parent_location->id . '/';
				}
			}

			$location->save();
		}

		// Log the history
		$this->historyRepository->add('pages', $page->id, $user_id, 'initial_version');

		return $page;
	}

    /**
     * @param $type
     * @param $user_id
     * @param bool $parent_page_id
     * @return PageModel
     */
    public function create($type, $user_id, $parent_pagelocation_id = false)
	{
		if ($parent_pagelocation_id)
		{
			$parent_location = $this->locationById($parent_pagelocation_id);

			if ($parent_location->page->pageType()->allowsSubPages())
			{
				return $this->createNewPage($type, false, $user_id, $parent_location->id);
			}			

			throw new SubPagesNotAllowed('This page type does not allow sub pages');
		}
		else
		{
			return $this->createNewPage($type, false, $user_id, $parent_pagelocation_id);
		}
	}

	public function createHome($type, $user_id)
	{
		// Check we don't already have a home page...
		$home = $this->getHomePage();

		if (!$home)
		{
			return $this->createNewPage($type, true, $user_id);	
		}

		throw new HomePageAlreadyExists('You already have a home page defined');
	}

    /**
     * @param $page_id
     * @param $version
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageNotFound
     * @throws \CoandaCMS\Coanda\Exceptions\PageVersionNotFound
     */
    public function getDraftVersion($page_id, $version)
	{
		$page = PageModel::find($page_id);

		if ($page)
		{
			$version = $page->versions()->whereStatus('draft')->whereVersion($version)->first();

			if ($version)
			{
				// Let the version update/check its attributes against the definition (which might have changed)
				$version->checkAttributes();

				return $version;
			}

			throw new PageVersionNotFound;
		}

		throw new PageNotFound;
	}

    /**
     * @param $id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageVersionNotFound
     */
    public function getVersionById($id)
	{
		$version = PageVersionModel::find($id);

		if (!$version)
		{
			throw new PageVersionNotFound;
		}

		return $version;
	}

    /**
     * @param $preview_key
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageVersionNotFound
     */
    public function getVersionByPreviewKey($preview_key)
	{
		$version = PageVersionModel::wherePreviewKey($preview_key)->whereStatus('draft')->first();

		if (!$version)
		{
			throw new PageVersionNotFound;
		}

		return $version;
	}

    /**
     * @param $version
     * @param $data
     * @throws \CoandaCMS\Coanda\Exceptions\ValidationException
     */
    public function saveDraftVersion($version, $data)
	{
		$failed = [];

		foreach ($version->attributes as $attribute)
		{
			try
			{
				$attribute->store($data['attribute_' . $attribute->id]);
			}
			catch (AttributeValidationException $exception)
			{
				$failed['attribute_' . $attribute->id] = $exception->getMessage();
			}
		}

		// If we are dealing with the home page, then the slug doesn't matter
		if (!$version->page->is_home)
		{
			// Check each of the locations to see if the slug is OK
			foreach ($version->page->locations as $location)
			{
				try
				{
					$this->urlRepository->canUse($location->slug . $data['slug_' . $location->id], 'pagelocation', $location->id);

					$version->setLocationSlug($location->id, $data['slug_' . $location->id]);

				}
				catch(\CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug $exception)
				{
					$failed['slug_' . $location->id] = 'The slug is not valid';
				}
				catch(\CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists $exception)
				{
					$failed['slug_' . $location->id] = 'The slug is already in use';
				}
			}
		}

		// Get the meta
		if ($version->page->show_meta)
		{
			$version->meta_page_title = $data['meta_page_title'];
			$version->meta_description = $data['meta_description'];
		}

		// Get the visible_from and to dates
		$format = isset($data['date_format']) ? $data['date_format'] : false;

		if ($format)
		{
			$dates = [
					'visible_from' => false,
					'visible_to' => false
				];

			$date_error = false;

			foreach (array_keys($dates) as $date)
			{
				if (isset($data[$date]) && $data[$date] !== '')
				{
					try
					{
						$dates[$date] = Carbon::createFromFormat($format, $data[$date], date_default_timezone_get());

						if ($dates[$date]->isPast())
						{
							$failed[$date] = 'The specified date is in past';

							$date_error = true;
						}
					}
					catch(\InvalidArgumentException $exception)
					{
						$failed[$date] = 'The specified date is invalid';

						$date_error = true;
					}
				}
			}

			if (!$date_error && $dates['visible_from'] && $dates['visible_to'])
			{
				// Check that the from date is before the to date
				if (!$dates['visible_from']->lt($dates['visible_to']))
				{
					$failed['visible_to'] = 'The date must be after the visible from date';
				}
			}

			if ($dates['visible_from'])
			{
				$version->visible_from = $dates['visible_from'];
			}

			if ($dates['visible_to'])
			{
				$version->visible_to = $dates['visible_to'];
			}

			// If we have a blank date, null it
			if (isset($data['visible_from']) && $data['visible_from'] == '')
			{
				$version->visible_from = null;
			}

			// If we have a blank date, null it
			if (isset($data['visible_to']) && $data['visible_to'] == '')
			{
				$version->visible_to = null;
			}
		}

		if (isset($data['layout_identifier']))
		{
			if ($data['layout_identifier'] !== '')
			{
				$layout = Coanda::module('layout')->layoutByIdentifier($data['layout_identifier']);

				if ($layout)
				{
					$version->layout_identifier = $data['layout_identifier'];
				}
			}
			else
			{
				$version->layout_identifier = '';
			}
		}

		$version->save();

		if (count($failed) > 0)
		{
			throw new ValidationException($failed);
		}
	}

    /**
     * @param $version
     */
    public function discardDraftVersion($version)
	{
		$page = $version->page;

		// Log the history
		$this->historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'discard_version', ['version' => $version->version]);

		$version->delete();

		// If now have no versions, then remove the page too
		if ($page->versions->count() == 0)
		{
			// Log the history
			$this->historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'page_deleted');

			$page->delete();
		}
	}

    /**
     * @param $page_id
     * @param $user_id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageNotFound
     */
    public function draftsForUser($page_id, $user_id)
	{
		$page = PageModel::find($page_id);

		if ($page)
		{
			return $page->versions()->whereStatus('draft')->whereCreatedBy($user_id)->get();
		}

		throw new PageNotFound;
	}

    /**
     * @param $version
     * @param $user_id
     * @param $urlRepository
     * @param $historyRepository
     */
    public function publishVersion($version, $user_id, $urlRepository, $historyRepository)
	{
		$page = $version->page;

		if ((int)$version->version !== 1)
		{
			// set the current published version to be archived
			$page->currentVersion()->status = 'archived';
			$page->currentVersion()->save();			
		}

		// set this version to be published
		$version->status = 'published';
		$version->save();
		
		// update the page name attribute (via the type)
		$page->name = $page->pageType()->generateName($version);
		$page->current_version = $version->version;
		$page->save();

		// If we are dealing with the home page, not need to worry about the URL...
		if (!$version->page->is_home)
		{
			// Register the URL for this version with the Url Repo
			foreach ($version->page->locations as $location)
			{
				$base_slug = $location->base_slug;

				if ($base_slug !== '')
				{
					$base_slug .= '/';
				}

				$url = $urlRepository->register($base_slug . $version->slugForLocation($location->id), 'pagelocation', $location->id);
			}
		}

		// Log the history
		$historyRepository->add('pages', $page->id, $user_id, 'publish_version', ['version' => (int)$version->version]);		
	}

    /**
     * @param $version
     */
    public function executePublishHandler($version, $publish_handler, $data)
	{
		$publish_handler = Coanda::module('pages')->getPublishHandler($publish_handler);

		if ($publish_handler)
		{
			$version->publish_handler = $publish_handler->identifier;

			// Validate the publish handler - this can throw an exception if needs be!
			$publish_handler->validate($version, $data);

			// Return the result of the publish handler - this should be a redirect URL of null/false as required.
			return $publish_handler->execute($version, $data, $this, $this->urlRepository, $this->historyRepository);
		}
	}

    /**
     * @param $page_id
     * @param $user_id
     * @return mixed
     */
    public function createNewVersion($page_id, $user_id)
	{
		$page = PageModel::find($page_id);
		$type = $page->pageType();

		$current_version = $page->currentVersion();
		$latest_version = $page->versions()->orderBy('version', 'desc')->first();

		$new_version_number = $latest_version->version + 1;

		// Create the version
		$version = new PageVersionModel;
		$version->version = $new_version_number;
		$version->status = 'draft';
		$version->created_by = $user_id;
		$version->edited_by = $user_id;

		// Carry over the meta
		$version->meta_page_title = $current_version->meta_page_title;
		$version->meta_description = $current_version->meta_description;

		// Carry over the visible date
		$version->visible_from = $current_version->visible_from;
		$version->visible_to = $current_version->visible_to;

		// Carry over the layout
		$version->layout_identifier = $current_version->layout_identifier;

		// Ask the layout module to replicate the custom region blocks for this
		Coanda::module('layout')->copyCustomRegionBlock('pages', $page->id . ':' . $current_version->version, $page->id . ':' . $new_version_number);

		$page->versions()->save($version);

		// Now lets replicate the slugs
		foreach ($current_version->slugs as $slug)
		{
			$new_slug = new $this->page_version_slug_model;
			$new_slug->version_id = $version->id;
			$new_slug->slug = $slug->slug;
			$new_slug->location_id = $slug->location_id;
			$new_slug->save();
		}

		// Add all the attributes..
		$index = 1;

		foreach ($type->attributes() as $type_attribute)
		{
			$page_attribute_type = Coanda::getAttributeType($type_attribute['type']);

			$attribute = new PageAttributeModel;

			$attribute->type = $page_attribute_type->identifier();
			$attribute->identifier = $type_attribute['identifier'];
			$attribute->order = $index;

			// Copy the attribute data from the current version
			$existing_attribute = $current_version->getAttributeByIdentifier($type_attribute['identifier']);

			$attribute->attribute_data = $existing_attribute ? $existing_attribute->attribute_data : '';

			$version->attributes()->save($attribute);

			$index ++;
		}

		// Log the history
		$this->historyRepository->add('pages', $page_id, Coanda::currentUser()->id, 'new_version', ['version' => $new_version_number]);

		return $new_version_number;
	}

    /**
     * @param $page_id
     * @param int $limit
     * @return mixed
     */
    public function recentHistory($page_id, $limit = 10)
	{
		return $this->historyRepository->get('pages', $page_id, $limit);
	}

    /**
     * @param $page_id
     * @param int $limit
     * @return mixed
     */
    public function history($page_id)
	{
		return $this->historyRepository->getPaginated('pages', $page_id);
	}

    /**
     * @param $page_id
     * @return mixed
     */
    public function contributors($page_id)
	{
		return $this->historyRepository->users('pages', $page_id);
	}

    /**
     * @param $page_id
     * @param bool $permanent
     * @throws \CoandaCMS\Coanda\Exceptions\PageNotFound
     */
    public function deletePage($page_id, $permanent = false)
	{
		$page = $this->model->find($page_id);

		if (!$page)
		{
			throw new PageNotFound;
		}

		if ($permanent)
		{
			$this->deleteSubPages($page, true);			

			foreach ($page->locations as $location)
			{
				$this->deleteLocation($location);
			}

			// Finally, we can remove this page
			$page->delete();

			$this->historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'deleted');
		}
		else
		{
			if (!$page->is_trashed)
			{
				$this->deleteSubPages($page, false);

				$page->is_trashed = true;
				$page->save();

				$this->historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'trashed');
			}
		}
	}

	public function deleteLocation($location)
	{
		$this->urlRepository->delete('pagelocation', $location->id);

		$location->delete();
	}

    /**
     * @param $page_ids
     * @param bool $permanent
     */
    public function deletePages($page_ids, $permanent = false)
	{
		if (count($page_ids) > 0)
		{
			foreach ($page_ids as $page_id)
			{
				try
				{
					$this->deletePage($page_id, $permanent);
				}
				catch (PageNotFound $exception)
				{
				}
			}
		}
	}

    /**
     * @param $page
     * @param bool $permanent
     */
    private function deleteSubPages($page, $permanent = false)
	{
		// Loop through the locations and set the sub pages to be deleted
		if ($page->locations->count() > 0)
		{
			foreach ($page->locations as $location)
			{
				$base_path = $location->path == '' ? '/' : $location->path;

				$sub_page_ids = $this->page_location_model->where('path', 'like', $base_path . $location->id . '/%')->lists('page_id');

				if (count($sub_page_ids) > 0)
				{
					if ($permanent)
					{
						foreach ($sub_page_ids as $sub_page_id)
						{
							$page = $this->model->find($sub_page_id);

							if ($page)
							{
								if ($page->locations->count() > 0)
								{
									foreach ($page->locations as $location)
									{
										$this->deleteLocation($location);
									}									
								}

								$page->delete();
							}
						}
					}
					else
					{
						$this->model->whereIn('id', $sub_page_ids)->update(['is_trashed' => true]);	
					}
				}
			}
		}
		// if ($permanent)
		// {
		// 	$pages = $this->model->where('path', 'like', $base_path . $page->id . '/%')->get();

		// 	foreach ($pages as $page)
		// 	{
		// 		$this->urlRepository->delete('page', $page->id);

		// 		$page->delete();	
		// 	}
		// }
		// else
		// {
		// 	$this->model->where('path', 'like', $base_path . $page->id . '/%')->update(['is_trashed' => true]);		
		// }
	}

    /**
     * @return mixed
     */
    public function trashed()
	{
		return $this->model->whereIsTrashed(true)->get();
	}

    /**
     * @param $page_id
     * @param bool $restore_sub_pages
     * @throws \CoandaCMS\Coanda\Exceptions\PageNotFound
     */
    public function restore($page_id, $restore_sub_pages = [])
	{
		$page = $this->model->find($page_id);

		if (!$page)
		{
			throw new PageNotFound;
		}

		if ($page->locations->count() > 0)
		{
			foreach ($page->locations as $location)
			{
				// Is the parent page trashed? If so, we need to restore that - which will be recursive if its parent is trashed...
				if ($location->parent)
				{
					if ($location->parent->page->is_trashed)
					{
						$this->restore($location->parent->page->id);
					}
				}

				// Are we restoring the sub pages?
				if (in_array($location->id, $restore_sub_pages))
				{
					$base_path = $location->path == '' ? '/' : $location->path;

					$sub_page_ids = $this->page_location_model->where('path', 'like', $base_path . $location->id . '/%')->lists('page_id');

					if (count($sub_page_ids) > 0)
					{
						$this->model->whereIn('id', $sub_page_ids)->update(['is_trashed' => false]);
					}			
				}
			}
		}

		$page->is_trashed = false;
		$page->save();

		$this->historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'restored');
	}

    /**
     * @param $new_orders
     */
    public function updateOrdering($new_orders)
	{
		foreach ($new_orders as $location_id => $new_order)
		{
			$this->page_location_model->whereId($location_id)->update(['order' => $new_order]);
			$this->historyRepository->add('pages', $location_id, Coanda::currentUser()->id, 'order_changed', ['new_order' => $new_order]);
		}
	}

    /**
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getPendingVersions($offset, $limit)
	{
		return PageVersionModel::whereStatus('pending')->take($limit)->offset($offset)->get();
	}

	public function getHomePage()
	{
		return $this->model->whereIsHome(true)->first();
	}
}