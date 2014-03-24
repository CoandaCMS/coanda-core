<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent;

use Coanda;

use CoandaCMS\Coanda\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;
use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Exceptions\PermissionDenied;

use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page as PageModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion as PageVersionModel;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute as PageAttributeModel;

use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;
use CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface;

class EloquentPageRepository implements PageRepositoryInterface {

	private $model;
	private $urlRepository;

	public function __construct(PageModel $model, \CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface $urlRepository)
	{
		$this->model = $model;
		$this->urlRepository = $urlRepository;
	}

	/**
	 * Tries to find the Eloquent page model by the id
	 * @param  integer $id
	 * @return Array
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
	 * Create a new page of the specified type for the user id
	 * @param  string $type
	 * @param  integer $user_id
	 * @return Page
	 */
	public function create($type, $user_id)
	{
		// create a page model
		$page = new PageModel;
		$page->type = $type->identifier;
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
			$page_attribute_type = Coanda::getPageAttributeType($type_attribute['type']);

			$attribute = new PageAttributeModel;

			$attribute->type = $page_attribute_type->identifier;
			$attribute->identifier = $type_attribute['identifier'];
			$attribute->order = $index;

			$version->attributes()->save($attribute);

			$index ++;
		}

		return $page;
	}

	/**
	 * Gets the draft version
	 * @param  integer $page_id
	 * @param  integer $version
	 * @return Page
	 */
	public function getDraftVersion($page_id, $version)
	{
		$page = PageModel::find($page_id);

		if ($page)
		{
			$version = $page->versions()->whereStatus('draft')->whereVersion($version)->first();

			if ($version)
			{
				return $version;
			}

			throw new PageVersionNotFound;
		}

		throw new PageNotFound;
	}

	/**
	 * Stores the data for the version
	 * @param  Version $version The version object
	 * @param  Array $data    All the data to be stored
	 * @return void
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
			$this->urlRepository->canUse($data['slug'], 'page', $version->page->id);
			
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

		$version->save();

		if (count($failed) > 0)
		{
			throw new ValidationException($failed);
		}
	}

	public function publishVersion($version)
	{
		$page = $version->page;

		if ($version->version !== 1)
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

		// Register the URL for this version with the Url Repo
		$url = $this->urlRepository->register($version->slug, 'page', $page->id);
	}

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

		$page->versions()->save($version);

		// Add all the attributes..
		$index = 1;

		foreach ($type->attributes() as $type_attribute)
		{
			$page_attribute_type = Coanda::getPageAttributeType($type_attribute['type']);

			$attribute = new PageAttributeModel;

			$attribute->type = $page_attribute_type->identifier;
			$attribute->identifier = $type_attribute['identifier'];
			$attribute->order = $index;

			// Copy the attribute data from the current version
			$attribute->attribute_data = $current_version->getAttributeByIdentifier($type_attribute['identifier'])->attribute_data;

			$version->attributes()->save($attribute);

			$index ++;
		}

		return $new_version_number;
	}
}