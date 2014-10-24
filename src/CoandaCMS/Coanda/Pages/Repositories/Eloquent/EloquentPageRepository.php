<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent;

use Coanda, Config, Input;

use CoandaCMS\Coanda\Pages\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;
use CoandaCMS\Coanda\Exceptions\ValidationException;

use CoandaCMS\Coanda\Pages\Exceptions\HomePageAlreadyExists;

use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries\QueryHandler;
use CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug;
use CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists;
use CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound;

use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageLocation as PageLocationModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page as PageModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion as PageVersionModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersionSlug as PageVersionSlugModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersionComment as PageVersionCommentModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute as PageAttributeModel;

use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;

use Carbon\Carbon;
use CoandaCMS\Coanda\Urls\Slugifier;

class EloquentPageRepository implements PageRepositoryInterface {

    /**
     * @var Models\Page
     */
    private $page_model;

    /**
     * @var Models\PageVersion
     */
    private $page_version_model;

    /**
     * @var Models\PageAttribute
     */
    private $page_attribute_model;

    /**
     * @var Models\PageVersionComment
     */
    private $page_version_comment_model;

    /**
     * @var \CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface
     */
    private $urls;

    /**
     * @param PageModel $page_model
     * @param PageVersionModel $page_version_model
     * @param PageAttributeModel $page_attribute_model
     * @param Models\PageVersionComment $page_version_comment_model
     * @param \CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface $urlRepository
     */
    public function __construct(PageModel $page_model, PageVersionModel $page_version_model, PageAttributeModel $page_attribute_model, PageVersionCommentModel $page_version_comment_model, \CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface $urlRepository)
	{
		$this->page_version_model = $page_version_model;
		$this->page_attribute_model = $page_attribute_model;
		$this->page_version_comment_model = $page_version_comment_model;
		$this->page_model = $page_model;
		
		$this->urls = $urlRepository;
	}

	/**
	 * @return PageModel
     */
	public function getPageModel()
    {
        return $this->page_model;
    }

    /**
     * @param $what
     * @param $identifier
     * @param string $data
     * @param bool $user_id
     */
    private function logHistory($what, $identifier, $data = '', $user_id = false)
	{
		if (!$user_id)
		{
            try
            {
                $user_id = Coanda::currentUser()->id;
            }
            catch (\CoandaCMS\Coanda\Exceptions\NotLoggedIn $exception)
            {
                $user_id = 0;
            }
		}

        \Event::fire('history.log', ['pages', $identifier, $user_id, $what, $data]);
	}

    /**
     * @param $id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageNotFound
     */
    public function find($id)
	{
		return $this->findById($id);
	}

    /**
     * @param $limit
     * @param $offset
     * @return mixed
     */
    public function get($limit, $offset)
    {
        return $this->page_model->take($limit)->skip($offset)->get();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageNotFound
     */
    public function findById($id)
	{
		$page = $this->page_model->find($id);

		if (!$page)
		{
			throw new PageNotFound('Page #' . $id . ' not found');
		}
		
		return $page;
	}

    /**
     * @param $slug
     * @return bool|mixed
     */
    public function findBySlug($slug)
	{
		try
		{
			$url = $this->urlRepository->findBySlug($slug);

			if ($url->type == 'page')
			{
				return $this->findById($url->type_id);
			}
		}
		catch (UrlNotFound $exception)
		{
			return false;
		}
	}

    /**
     * @param $remote_id
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageNotFound
     * @internal param $id
     * @return mixed
     */
	public function getByRemoteId($remote_id)
	{
		$page = $this->page_model->whereRemoteId($remote_id)->first();

		if (!$page)
		{
			throw new PageNotFound('Page with remote id: ' . $remote_id . ' not found');
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
			$page = $this->page_model->find($id);

			if ($page)
			{
				$pages->add($page);
			}
		}

		return $pages;
	}

    /**
     * @param $parent_page_id
     * @param $current_page
     * @param int $per_page
     * @param array $parameters
     * @internal param $parent_location_id
     * @return mixed
     */
    public function subPages($parent_page_id, $current_page, $per_page = 10, $parameters = [])
	{
		$query = new QueryHandler($this);

		return $query->subPages([
				'parent_page_id' => $parent_page_id,
				'current_page' => $current_page,
				'per_page' => $per_page,
				'parameters' => $parameters
			]);
	}

    /**
     * @param $type
     * @param $is_home
     * @param $user_id
     * @param bool $parent_page_id
     * @return mixed
     */
    private function createNewPage($type, $is_home, $user_id, $parent_page_id = false)
	{
		$page = $this->page_model->create([
            'parent_page_id' => $parent_page_id ? $parent_page_id : 0,
            'is_home' => $is_home,
            'type' => $type->identifier(),
            'created_by' => $user_id,
            'edited_by' => $user_id,
            'current_version' => 1
        ]);

		$version = $this->page_version_model->create([
            'page_id' => $page->id,
            'version' => 1,
            'status' => 'draft',
            'created_by' => $user_id,
            'edited_by' => $user_id,
        ]);

		$index = 1;

		foreach ($type->attributes() as $type_attribute_identifier => $type_attribute)
		{
			$page_attribute_type = Coanda::getAttributeType($type_attribute['type']);

			$this->page_attribute_model->create([
                'page_version_id' => $version->id,
                'identifier' => $type_attribute_identifier,
                'type' => $page_attribute_type->identifier(),
                'order' => $index
            ]);

			$index ++;
		}

		// Log the history
		$this->logHistory('initial_version', $page->id, '', $user_id);

		return $page;
	}

	/**
	 * @param $type
	 * @param $user_id
	 * @return mixed
	 * @throws \CoandaCMS\Coanda\Pages\Exceptions\HomePageAlreadyExists
	 */
	public function createHome($type, $user_id)
	{
		return $this->createNewPage($type, true, $user_id);
	}


	/**
     * @param $type
     * @param $user_id
     * @param bool $parent_page_id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\SubPagesNotAllowed
     */
    public function create($type, $user_id, $parent_page_id = false)
	{
        return $this->createNewPage($type, false, $user_id, $parent_page_id);
	}

	/**
	 * @param $type
	 * @param $user_id
	 * @param $page_data
	 * @return mixed
	 */
	public function createAndPublishHome($type, $user_id, $page_data)
	{
		$page = $this->createHome($type, $user_id);
		$version = $page->currentVersion();

		$this->saveDraftVersion($version, $page_data);
		$this->publishVersion($version, $user_id, $this->urlRepository);

		return $page;
	}


	/**
     * @param $type
     * @param $user_id
     * @param $parent_page_id
     * @param $page_data
     * @return mixed
     */
    public function createAndPublish($type, $user_id, $parent_page_id, $page_data)
	{
		if (is_string($type))
		{
			$type = Coanda::module('pages')->getPageType($type);
		}

		$page = $this->create($type, $user_id, $parent_page_id);

		if (isset($page_data['remote_id']))
		{
			$page->setRemoteId($page_data['remote_id']);
		}

		$version = $page->currentVersion();

		$this->saveDraftVersion($version, $page_data);
		$this->publishVersion($version, $user_id, $this->urlRepository);

		$page = $this->find($page->id);

		// Add the slug data
		if (isset($page_data['order']))
		{
            $page->order = $page_data['order'];
            $page->save();
		}

		return $page;
	}

    /**
     * @param $page
     * @param $user_id
     * @param $parent_page_id
     * @param $page_data
     * @return mixed
     */
    public function updateAndPublish($page, $user_id, $parent_page_id, $page_data)
	{
		$version_number = $this->createNewVersion($page->id, $user_id);
		$version = $page->getVersion($version_number);

		$version = $this->getVersionById($version->id);

		$this->saveDraftVersion($version, $page_data);
		$this->publishVersion($version, $user_id, $this->urlRepository);

		$page = $this->find($page->id);

		return $page;
	}

    /**
     * @param $page
     * @param $user_id
     */
    public function hideAndPublish($page, $user_id)
	{
		$version_number = $this->createNewVersion($page->id, $user_id);
		$version = $page->getVersion($version_number);

		$version->is_hidden = true;

		$this->publishVersion($version, $user_id, $this->urlRepository);
	}

    /**
     * @param $page_id
     * @param $version
     * @return mixed
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageNotFound
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound
     */
    public function getDraftVersion($page_id, $version)
	{
		$page = $this->page_model->find($page_id);

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
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound
     */
    public function getVersionById($id)
	{
		$version = $this->page_version_model->find($id);

		if (!$version)
		{
			throw new PageVersionNotFound;
		}

		return $version;
	}

    /**
     * @param $preview_key
     * @return mixed
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound
     */
    public function getVersionByPreviewKey($preview_key)
	{
		$version = $this->page_version_model->wherePreviewKey($preview_key)->first();

		if (!$version)
		{
			throw new PageVersionNotFound;
		}

		return $version;
	}

	/**
	 * @param $page_id
	 * @param $limit
	 * @param $offset
	 * @return mixed
	 * @throws PageNotFound
     */
	public function getVersionsForPage($page_id, $limit, $offset)
    {
        $page = $this->page_model->find($page_id);

        if ($page)
        {
            return $page->versions()->take($limit)->skip($offset)->get();
        }

        throw new PageNotFound('Page #' . $page_id . ' not found.');
    }

	/**
	 * @param $page_id
	 * @return mixed
	 * @throws PageNotFound
     */
	public function getVersionCountForPage($page_id)
    {
        $page = $this->page_model->find($page_id);

        if ($page)
        {
            return $page->versions()->count();
        }

        throw new PageNotFound('Page #' . $page_id . ' not found.');
    }

    /**
     * @param $version
     * @param $data
     * @return mixed|void
     * @throws \CoandaCMS\Coanda\Exceptions\ValidationException
     */
    public function saveDraftVersion($version, $data)
	{
		$failed = [];

        list($data, $failed) = $this->saveAttributes($version, $data, $failed);
        list($data, $failed) = $this->saveVisibleDates($version, $data, $failed);
        list($data, $failed) = $this->saveSlug($version, $data, $failed);

        $this->saveMeta($version, $data);
        $this->saveTemplate($version, $data);
        $this->saveLayout($version, $data);
        $this->saveVisibility($version, $data);

		$version->save();

		if (count($failed) > 0)
		{
			throw new ValidationException($failed, $version->id);
		}
	}

	/**
	 * @param $version
	 * @param $data
	 * @param $failed
	 * @return array
     */
	private function saveSlug($version, $data, $failed)
    {
		if (!$version->page->is_home)
		{
            if ($data['slug'] == '')
            {
                $version->slug = $this->generateSlug($version);
            }
            else
            {
                $version->slug = $data['slug'];

                try
                {
                    if (!$this->urls->canUse($version->full_slug, 'page', $version->page->id))
                    {
                        $failed['slug'] = 'Slug is already in use.';
                    }

                }
                catch (InvalidSlug $exception)
                {
                    $failed['slug'] = 'Slug is not valid';
                }
            }
		}

        return [$data, $failed];
    }


	/**
	 * @param $version
	 * @param $data
	 * @return mixed
     */
	private function saveMeta($version, $data)
    {
        // Get the meta
        if ($version->page->show_meta)
        {
            $version->meta_page_title = isset($data['meta_page_title']) ? $data['meta_page_title'] : '';
            $version->meta_description = isset($data['meta_description']) ? $data['meta_description'] : '';
        }

        return $version;
    }

	/**
	 * @param $version
	 * @param $data
	 * @return mixed
     */
	private function saveTemplate($version, $data)
    {
		if (isset($data['template_identifier']))
		{
            $version->template_identifier = ($data['template_identifier'] !== '') ? $data['template_identifier'] : '';
		}

        return $version;
    }

	/**
	 * @param $version
	 * @param $data
	 * @return mixed
     */
	private function saveLayout($version, $data)
    {
        if (isset($data['layout_identifier']))
        {
            $version->layout_identifier = ($data['layout_identifier'] !== '') ? $data['layout_identifier'] : '';
        }

        return $version;
    }

	/**
	 * @param $version
	 * @param $data
     */
	private function saveVisibility($version, $data)
    {
        $version->is_hidden = (isset($data['is_hidden']) && ($data['is_hidden'] == 'yes' || $data['is_hidden'] === true)) ? true : false;
        $version->is_hidden_navigation = (isset($data['is_hidden_navigation']) && ($data['is_hidden_navigation'] == 'yes' || $data['is_hidden_navigation'] === true)) ? true : false;
    }

    /**
     * @param $version
     * @param $user_id
     * @return mixed|void
     */
    public function discardDraftVersion($version, $user_id)
	{
		$page = $version->page;
		$version->delete();

		// If now have no versions, then remove the page too
		if ($page->versions->count() == 0)
		{
			$page->delete();
		}
	}

    /**
     * @param $page_id
     * @param $user_id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageNotFound
     */
    public function draftsForUser($page_id, $user_id)
	{
		$page = $this->page_model->find($page_id);

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
     * @return mixed|void
     */
    public function publishVersion($version, $user_id, $urlRepository)
	{
		$page = $version->page;

        // First up, make sure the URL is OK...
        if (!$version->page->is_home)
        {
            $urlRepository->register($version->full_slug, 'page', $version->page->id);
        }

		if ((int) $version->version !== 1)
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

		// Log the history
		$this->logHistory('publish_version', $page->id, ['version' => (int)$version->version], $user_id);

        if ($version->is_hidden)
        {
            $this->unRegisterPageWithSearchProvider($page);
        }
        else
        {
            $this->registerPageWithSearchProvider($page);
        }
	}


    /**
     * @param $page
     */
    public function registerPageWithSearchProvider($page)
	{
		$version = $page->currentVersion();

		$search_data = [
			'page_type' => $page->type,
			'name' => $page->name
		];

		$visible_from = (string) $version->visible_from;

		if ($visible_from !== '')
		{
			$search_data['visible_from'] = $visible_from;
		}

		$visible_to = (string) $version->visible_to;

		if ($visible_to !== '')
		{
			$search_data['visible_to'] = $visible_to;
		}

		foreach ($page->attributes as $attribute)
		{
			$search_data[$attribute->identifier] = $attribute->render($page, true);
		}

		Coanda::search()->register('pages', $page->id, $page->slug, $search_data);
	}

    /**
     * @param $page
     */
    public function unRegisterPageWithSearchProvider($page)
	{
		Coanda::search()->unRegister('pages', $page->id);
	}

    /**
     * @param $version
     * @param $publish_handler
     * @param $data
     * @return mixed
     */
    public function executePublishHandler($version, $publish_handler, $data)
	{
        $version->publish_handler = $publish_handler->identifier;

        // Validate the publish handler - this can throw an exception if needs be!
        $publish_handler->validate($version, $data);

        // Return the result of the publish handler - this should be a redirect URL of null/false as required.
        return $publish_handler->execute($version, $data, $this, $this->urls);
	}

    /**
     * @param $page_id
     * @param $user_id
     * @param bool $base_version_number
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound
     * @return mixed
     */
    public function createNewVersion($page_id, $user_id, $base_version_number = false)
	{
		$page = $this->page_model->find($page_id);
		$type = $page->pageType();

		if ($base_version_number)
		{
			$current_version = $page->getVersion($base_version_number);
		}
		else
		{
			$current_version = $page->currentVersion();	
		}

		if (!$current_version)
		{
			throw new PageVersionNotFound('Version #' . $base_version_number . ' could not be found');
		}

		$latest_version = $page->versions()->orderBy('version', 'desc')->first();

		$new_version_number = $latest_version->version + 1;

		// Create the new version...
		$version_data = [
			'page_id' => $page->id,
            'slug' => $current_version->slug,
			'version' => $new_version_number,
			'status' => 'draft',
			'is_hidden' => $current_version->is_hidden,
			'is_hidden_navigation' => $current_version->is_hidden_navigation,
			'created_by' => $user_id,
			'edited_by' => $user_id,
			'meta_page_title' => $current_version->meta_page_title,
			'meta_description' => $current_version->meta_description,
			'visible_from' => $current_version->visible_from,
			'visible_to' => $current_version->visible_to,
			'template_identifier' => $current_version->template_identifier,
			'layout_identifier' => $current_version->layout_identifier
		];

		$version = $this->page_version_model->create($version_data);

		// Add all the attributes..
		$index = 1;

		foreach ($type->attributes() as $type_attribute_identifier => $type_attribute)
		{
			$page_attribute_type = Coanda::getAttributeType($type_attribute['type']);

			// Copy the attribute data from the current version
			$existing_attribute = $current_version->getAttributeByIdentifier($type_attribute_identifier);
			$attribute_value = '';

			if ($existing_attribute)
			{
				$attribute_value = $existing_attribute->attribute_data;
			}
			else
			{
				// Add the default value...
				if (isset($type_attribute['default']))
				{
					try
					{
						$attribute_value = $page_attribute_type->store($type_attribute['default'], false, '');
					}
					catch (AttributeValidationException $exception)
					{
						// Do nothing...
					}
				}
			}
			
			$attribute_data = [
				'page_version_id' => $version->id,
				'identifier' => $type_attribute_identifier,
				'type' => $page_attribute_type->identifier(),
				'order' => $index,
				'attribute_data' => $attribute_value
			];

			$attribute = $this->page_attribute_model->create($attribute_data);

			$from_attribute_data = [];

			if ($existing_attribute)
			{
				$from_attribute_data = [
					'attribute_id' => $existing_attribute->id,
					'page_id' => $existing_attribute->page()->id,
					'version_number' => $existing_attribute->version->version
				];
			}

			$to_attribute_data = [
				'attribute_id' => $attribute->id,
				'page_id' => $attribute->page()->id,
				'version_number' => $attribute->version->version
			];

			$page_attribute_type->initialise($from_attribute_data, $to_attribute_data);

			$index ++;
		}

		// Log the history
		$this->logHistory('new_version', $page_id, ['version' => (int)$new_version_number], $user_id);

		return $new_version_number;
	}


    /**
     * @param $page_id
     * @param bool $permanent
     * @return mixed|void
     * @throws \CoandaCMS\Coanda\Pages\Exceptions\PageNotFound
     */
    public function deletePage($page_id, $permanent = false)
	{
		$page = $this->page_model->find($page_id);

		if (!$page)
		{
			return;
		}

		if ($permanent)
		{
			$this->deleteSubPages($page, true);
			$this->urls->delete('page', $page->id);
			$page->delete();

			$this->logHistory('deleted', $page->id);
		}
		else
		{
			if (!$page->is_trashed)
			{
				$this->deleteSubPages($page, false);

				$page->is_trashed = true;
				$page->save();

				$this->logHistory('trashed', $page->id);
			}
		}

        $this->unRegisterPageWithSearchProvider($page);
	}

    /**
     * @param $page_ids
     * @param bool $permanent
     * @return mixed|void
     */
    public function deletePages($page_ids, $permanent = false)
	{
		if (count($page_ids) > 0)
		{
			foreach ($page_ids as $page_id)
			{
				$this->deletePage($page_id, $permanent);
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
		$base_path = $page->path == '' ? '/' : $page->path;

		$sub_page_ids = $this->page_model->where('path', 'like', $base_path . $page->id . '/%')->lists('id');

		if (count($sub_page_ids) > 0)
		{
			if ($permanent)
			{
				foreach ($sub_page_ids as $sub_page_id)
				{
					$page = $this->page_model->find($sub_page_id);

					if ($page)
					{
						$this->urls->delete('page', $page->id);
						$page->delete();
					}
				}
			}
			else
			{
				$this->page_model->whereIn('id', $sub_page_ids)->update(['is_trashed' => true]);
			}
		}
	}

    /**
     * @return mixed
     */
    public function trashed()
	{
		return $this->page_model->whereIsTrashed(true)->get();
	}

	/**
	 * @param $page_id
	 * @param bool $restore_sub_pages
	 * @throws PageNotFound
     */
	public function restore($page_id, $restore_sub_pages = true)
	{
		$page = $this->page_model->find($page_id);

		if (!$page)
		{
			throw new PageNotFound;
		}

		$page->is_trashed = false;
		$page->save();

		if ($page->parent)
		{
			if ($page->parent->is_trashed)
			{
				$this->restore($page->parent->id);
			}
		}

		if ($restore_sub_pages)
		{
			$base_path = $page->path == '' ? '/' : $page->path;

			$sub_page_ids = $this->page_model->where('path', 'like', $base_path . $page->id . '/%')->lists('id');

			if (count($sub_page_ids) > 0)
			{
				$this->page_model->whereIn('id', $sub_page_ids)->update(['is_trashed' => false]);
			}
		}

		$this->logHistory('restored', $page->id);
	}

    /**
     * @param $page_id
     * @param $new_order
     * @return mixed|void
     * @internal param $new_orders
     */
    public function updatePageOrder($page_id, $new_order)
	{
		$this->page_model->whereId($page_id)->update(['order' => $new_order]);
	}

	/**
	 * @param $page_id
	 * @param $new_sub_page_order
	 * @return mixed|void
	 */
	public function updateSubPageOrder($page_id, $new_sub_page_order)
	{
		$this->page_model->whereId($page_id)->update(['sub_page_order' => $new_sub_page_order]);
	}

	/**
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getPendingVersions($offset, $limit)
	{
		return $this->page_version_model->whereStatus('pending')->take($limit)->offset($offset)->get();
	}

    /**
     * @return mixed
     */
    public function getHomePage()
	{
		return $this->page_model->whereIsHome(true)->first();
	}

    /**
     * @param $version
     * @param $action_data
     * @param $data
     */
    public function handleAttributeAction($version, $action_data, $data)
	{
		foreach ($version->attributes as $attribute)
		{
			if (array_key_exists('attribute_' . $attribute->identifier, $action_data))
			{
				$attribute_data = isset($data['attribute_' . $attribute->identifier]) ? $data['attribute_' . $attribute->id] : false;
				$attribute->handleAction($action_data['attribute_' . $attribute->identifier], $attribute_data);
			}
		}
	}

    /**
     * @param $version
     * @param $data
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\ValidationException
     */
    public function addVersionComment($version, $data)
	{
		$invalid_fields = [];

		if (!$data['name'] || $data['name'] == '')
		{
			$invalid_fields['name'] = 'Please enter your name';
		}

		if (!$data['comment'] || $data['comment'] == '')
		{
			$invalid_fields['comment'] = 'Please enter a comment';
		}

		if (count($invalid_fields) > 0)
		{
			throw new ValidationException($invalid_fields);
		}

		$comment_data = [
			'version_id' => $version->id,
			'name' => $data['name'],
			'comment' => $data['comment']
		];

		$comment = $this->page_version_comment_model->create($comment_data);

		return $comment;
	}

    /**
     * @param $version
     * @return mixed|string
     */
    private function generateSlug($version)
	{
        $base_slug = $version->page->parent_slug;
        $page_id = $version->page->id;

		foreach ($version->attributes as $attribute)
		{
			if ($attribute->generates_slug)
			{
				$content = $attribute->content;

				if ($content && $content !== '')
				{
					$new_slug = Slugifier::convert($content);

                    $tries = 50;

                    for ($i = 0; $i < $tries; $i ++)
                    {
                        if ($i > 0)
                        {
                            $new_slug = $new_slug . '-' . $i;
                        }

                        if ($this->urls->canUse($base_slug . '/' . $new_slug, 'page', $page_id))
                        {
                            return $new_slug;
                        }
                    }
				}
			}
		}

		return '';
	}

    /**
     * @param $query
     * @return mixed
     */
    public function adminSearch($query)
	{
		if ($query && $query !== '')
		{
			return $this->page_model->where('name', 'like', '%' . $query . '%')->orderBy('name', 'asc')->paginate(10);	
		}
		
		return \Paginator::make([], 0, 10);
	}

    /**
     * @param $version
     * @param $data
     * @param $failed
     * @return array
     */
    private function saveAttributes($version, $data, $failed)
    {
        foreach ($version->attributes as $attribute)
        {
            try
            {
                $attribute_data = isset($data['attributes'][$attribute->identifier]) ? $data['attributes'][$attribute->identifier] : null;

                $attribute->store($attribute_data, 'attribute_' . $attribute->identifier);
            }
            catch (AttributeValidationException $exception)
            {
                $failed['attributes'][$attribute->identifier] = $exception->getMessage();
            }
        }

        return [$data, $failed];
    }

    /**
     * @param $version
     * @param $data
     * @param $failed
     * @return array
     */
    private function saveVisibleDates($version, $data, $failed)
    {
        $format = isset($data['date_format']) ? $data['date_format'] : Config::get('coanda::coanda.datetime_format');

        if ($format)
        {
            $dates = [
                'from' => false,
                'to' => false
            ];

            $date_error = false;

            foreach (array_keys($dates) as $date)
            {
                if (isset($data['visible_dates'][$date]) && $data['visible_dates'][$date] !== '')
                {
                    try
                    {
                        $dates[$date] = Carbon::createFromFormat($format, $data['visible_dates'][$date], date_default_timezone_get());
                    }
                    catch (\InvalidArgumentException $exception)
                    {
                        $failed['visible_dates_' . $date] = 'The specified date is invalid';
                    }
                }
            }

            if (!$date_error && $dates['from'] && $dates['to'])
            {
                // Check that the from date is before the to date
                if (!$dates['from']->lt($dates['to']))
                {
                    $failed['visible_dates_to'] = 'The date must be after the visible from date';
                }
            }

            if ($dates['from'])
            {
                $version->visible_from = $dates['from'];
            }

            if ($dates['to'])
            {
                $version->visible_to = $dates['to'];
            }

            // If we have a blank date, null it
            if (isset($data['visible_dates']['from']) && $data['visible_dates']['from'] == '')
            {
                $version->visible_from = null;
            }

            // If we have a blank date, null it
            if (isset($data['visible_dates']['to']) && $data['visible_dates']['to'] == '')
            {
                $version->visible_to = null;
            }
        }

        return [$data, $failed];
    }
}