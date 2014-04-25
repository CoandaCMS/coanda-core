<?php namespace CoandaCMS\Coanda\Urls\Repositories\Eloquent;

use CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists;
use CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug;
use CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound;

/**
 * Class EloquentUrlRepository
 * @package CoandaCMS\Coanda\Urls\Repositories\Eloquent
 */
class EloquentUrlRepository implements \CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface {

    /**
     * @var \CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url
     */
    private $model;
    /**
     * @var \CoandaCMS\Coanda\Urls\Slugifier
     */
    private $slugifier;

    private $db;

    /**
     * @param UrlModel $model
     * @param CoandaCMS\Coanda\Urls\Slugifier $slugifier
     */
    public function __construct(\CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url $model, \CoandaCMS\Coanda\Urls\Slugifier $slugifier, \Illuminate\Database\DatabaseManager $db)
	{
		$this->model = $model;
		$this->slugifier = $slugifier;
		$this->db = $db;
	}

    /**
     * @param $for
     * @param $for_id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound
     */
    public function findFor($for, $for_id)
	{
		$url = $this->model->whereUrlableType($for)->whereUrlableId($for_id)->first();

		if ($url)
		{
			return $url;
		}

		throw new UrlNotFound('Url not found (findFor)');
	}

	/**
	 * Tries to find the Eloquent URL model by the id
	 * @param  integer $id
	 * @return Array
	 */
	public function findById($id)
	{
		$url = $this->model->find($id);

		if (!$url)
		{
			throw new UrlNotFound('Url #' . $id . ' not found');
		}
		
		return $url;
	}

	/**
	 * Tries to find the Eloquent URL model by the slug
	 * @param  integer $id
	 * @return Array
	 */
	public function findBySlug($slug)
	{
		// Can we match this slug directly?
		$url = $this->model->whereSlug($slug)->first();

		if ($url)
		{
			return $url;
		}

		if (strpos($slug, '/'))
		{
			$slug_parts = explode('/', $slug);

			foreach ($slug_parts as $slug_part)
			{
				$url = $this->model->whereSlug($slug_part)->whereUrlableType('wildcard')->first();

				if ($url)
				{
					return $url;
				}
			}			
		}

		throw new UrlNotFound('Url for /' . $slug . ' not found');
	}

    /**
     * @param $slug
     * @param $for
     * @param $for_id
     * @return bool
     * @throws \CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists
     * @throws \CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug
     */
    public function register($slug, $for, $for_id)
	{
		// Is this a valid slug?
		if (!$this->slugifier->validate($slug))
		{
			throw new InvalidSlug('The requested slug is not valid');
		}

		// do we already have a record for this slug?
		$existing = $this->model->whereSlug($slug)->first();

		if ($existing)
		{
			// If the existing url matches the type and id, then we don't need to do anything..
			if ($existing->urlable_type == $for && $existing->urlable_id == $for_id)
			{
				return $existing;
			}

			// If the existing one is a url, then we can overwrite it, otherwise it is alreay taken.
			if ($existing->urlable_type !== 'wildcard')
			{
				throw new UrlAlreadyExists('The requested URL: ' . $slug . ' is already in use.');
			}
		}

		// Do we have a record for this urlable_type and urlable_id
		$current_url = $this->model->whereUrlableType($for)->whereUrlableId($for_id)->first();

		$url = $existing ? $existing : false;

		// If we don't have a URL, then create a new one
		if (!$url)
		{
			$url = $this->model->create(['slug' => $slug]);
		}

		$url->urlable_type = $for;
		$url->urlable_id = $for_id;

		$url->save();

		// If we have an existing url, then set it as a 'redirect' to the new url object
		if ($current_url)
		{
			// Update any child URL's to have the new slug
			$this->updateSubTree($current_url->slug, $slug);

			$current_url->urlable_type = 'wildcard';
			$current_url->urlable_id = $url->id;
			$current_url->save();
		}

		return $url;
	}

    /**
     * @param $slug
     * @param $new_slug
     */
    private function updateSubTree($slug, $new_slug)
	{
		$this->model->where('slug', 'like', $slug . '/%')->update(['slug' => $this->db->raw("REPLACE(slug, '" . $slug . "/', '" . $new_slug . "/')")]);
	}

    /**
     * @param $for
     * @param $for_id
     */
    public function delete($for, $for_id)
	{
		$url = $this->model->whereUrlableType($for)->whereUrlableId($for_id)->first();

		if ($url)
		{
			$url->delete();
		}
	}

    /**
     * @param $slug
     * @param $for
     * @param $for_id
     * @return bool
     * @throws \CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists
     * @throws \CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug
     */
    public function canUse($slug, $for, $for_id)
	{
		if (!$this->slugifier->validate($slug))
		{
			throw new InvalidSlug('The slug is not valid');
		}

		// do we already have a record for this slug?
		$existing = $this->model->whereSlug($slug)->first();

		if ($existing)
		{
			// If the existing matches the type and id, then we can use it
			if ($existing->urlable_type == $for && $existing->urlable_id == $for_id)
			{
				return true;
			}

			// If the exisitng type is a url, then it can be overwritten (otherwise this would be 'reserved' forever)
			if ($existing->urlable_type == 'wildcard')
			{
				return true;
			}

			throw new UrlAlreadyExists('The requested URL is already in use.');
		}
	}
}