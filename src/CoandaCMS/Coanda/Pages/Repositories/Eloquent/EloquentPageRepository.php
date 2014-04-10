<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent;

use Coanda;

use CoandaCMS\Coanda\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;
use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Pages\Exceptions\PublishHandlerException;

use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page as PageModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion as PageVersionModel;
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
    public function __construct(PageModel $model, \CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface $urlRepository, \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface $historyRepository)
	{
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

    /**
     * @param int $per_page
     * @return mixed
     */
    public function topLevel($per_page = 10)
	{
		return $this->model->where('parent_page_id', 0)->whereIsTrashed(false)->orderBy('order', 'asc')->paginate($per_page);
	}

    /**
     * @param $page_id
     * @param $per_page
     * @return mixed
     */
    public function subPages($page_id, $per_page)
	{
		return $this->model->where('parent_page_id', $page_id)->whereIsTrashed(false)->orderBy('order', 'asc')->paginate($per_page);
	}

    /**
     * @param $type
     * @param $user_id
     * @param bool $parent_page_id
     * @return PageModel
     */
    public function create($type, $user_id, $parent_page_id = false)
	{
		// create a page model
		$page = new PageModel;
		$page->type = $type->identifier;
		$page->created_by = $user_id;
		$page->edited_by = $user_id;
		$page->current_version = 1;

		if ($parent_page_id)
		{
			$parent_page = $this->model->find($parent_page_id);

			if ($parent_page)
			{
				$page->parent_page_id = $parent_page->id;
				$page->path = $parent_page->path . $parent_page->id . '/';
			}
		}

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
			$page_attribute_type = Coanda::module('pages')->getPageAttributeType($type_attribute['type']);

			$attribute = new PageAttributeModel;

			$attribute->type = $page_attribute_type->identifier;
			$attribute->identifier = $type_attribute['identifier'];
			$attribute->order = $index;

			$version->attributes()->save($attribute);

			$index ++;
		}

		// Log the history
		$this->historyRepository->add('pages', $page->id, $user_id, 'initial_version');

		return $page;
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

		// Lets check the requested slug
		try
		{
			$this->urlRepository->canUse($version->base_slug . $data['slug'], 'page', $version->page->id);
			
			$version->slug = $data['slug'];
		}
		catch(\CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug $exception)
		{
			$failed['slug'] = 'The slug is not valid';
		}
		catch(\CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists $exception)
		{
			$failed['slug'] = 'The slug is already in use';
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
     */
    public function publishVersion($version, $publish_handler)
	{
		$publish_handler = Coanda::module('pages')->getPublishHandler($publish_handler);

		if (!$publish_handler)
		{
			dd('no publish handler for: ' . $publish_handler);
		}

		// Validate the publish handler - this can throw an exception if needs be!
		$publish_handler->validate($version);

		// Return the result of the publish handler - this should be a redirect URL of null/false as required.
		return $publish_handler->execute($version, $this->urlRepository, $this->historyRepository);
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

		// Get the slug from the current version
		$version->slug = $current_version->slug;

		// Carry over the meta
		$version->meta_page_title = $current_version->meta_page_title;
		$version->meta_description = $current_version->meta_description;

		// Carry over the visible date
		$version->visible_from = $current_version->visible_from;
		$version->visible_to = $current_version->visible_to;

		$page->versions()->save($version);

		// Add all the attributes..
		$index = 1;

		foreach ($type->attributes() as $type_attribute)
		{
			$page_attribute_type = Coanda::module('pages')->getPageAttributeType($type_attribute['type']);

			$attribute = new PageAttributeModel;

			$attribute->type = $page_attribute_type->identifier;
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
			$this->urlRepository->delete('page', $page->id);

			$this->deleteSubTree($page, true);
			$page->delete();

			$this->historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'deleted');
		}
		else
		{
			if (!$page->is_trashed)
			{
				$page->is_trashed = true;
				$page->save();

				$this->deleteSubTree($page, false);

				$this->historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'trashed');
			}
		}
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
    private function deleteSubTree($page, $permanent = false)
	{
		$base_path = $page->path == '' ? '/' : $page->path;

		if ($permanent)
		{
			$pages = $this->model->where('path', 'like', $base_path . $page->id . '/%')->get();

			foreach ($pages as $page)
			{
				$this->urlRepository->delete('page', $page->id);

				$page->delete();	
			}
		}
		else
		{
			$this->model->where('path', 'like', $base_path . $page->id . '/%')->update(['is_trashed' => true]);		
		}
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function trashedParentsForPage($page_id)
	{
		$trashed_parents = new \Illuminate\Database\Eloquent\Collection;

		$page = $this->model->find($page_id);

		if ($page)
		{
			foreach ($page->parents() as $parent)
			{
				if ($parent->is_trashed)
				{
					$trashed_parents->add($parent);
				}
			}
		}

		return $trashed_parents;
	}

    /**
     * @param $page_id
     * @param bool $restore_sub_pages
     * @throws \CoandaCMS\Coanda\Exceptions\PageNotFound
     */
    public function restore($page_id, $restore_sub_pages = false)
	{
		$page = $this->model->find($page_id);

		if (!$page)
		{
			throw new PageNotFound;
		}

		// Do we have a parent page and does it need to be restored?
		$parent = $page->parent;

		if ($parent && $parent->is_trashed)
		{
			$this->restore($parent->id);
		}

		// Now we can update this page
		$page->is_trashed = false;
		$page->save();

		if ($restore_sub_pages)
		{
			$this->restoreSubTree($page->path);
		}

		$this->historyRepository->add('pages', $page->id, Coanda::currentUser()->id, 'restored');
	}

    /**
     * @param $path
     */
    public function restoreSubTree($path)
	{
		$this->model->where('path', 'like', $path . '%')->update(['is_trashed' => false]);
	}

    /**
     * @param $new_orders
     */
    public function updateOrdering($new_orders)
	{
		foreach ($new_orders as $page_id => $new_order)
		{
			$this->model->whereId($page_id)->update(['order' => $new_order]);
			$this->historyRepository->add('pages', $page_id, Coanda::currentUser()->id, 'order_changed', ['new_order' => $new_order]);
		}
	}
}