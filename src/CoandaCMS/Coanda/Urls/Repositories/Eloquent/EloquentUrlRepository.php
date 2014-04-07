<?php namespace CoandaCMS\Coanda\Urls\Repositories\Eloquent;

use Coanda;

use CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists;
use CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug;
use CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound;

use CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url as UrlModel;

class EloquentUrlRepository implements \CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface {

	private $model;
	private $slugifier;

	public function __construct(UrlModel $model, \CoandaCMS\Coanda\Urls\Slugifier $slugifier)
	{
		$this->model = $model;
		$this->slugifier = $slugifier;
	}

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
				return true;
			}

			// If the existing one is a url, then we can overwrite it, otherwise it is alreay taken.
			if ($existing->urlable_type !== 'redirect')
			{
				throw new UrlAlreadyExists('The requested URL is already in use.');
			}
		}

		// Do we have a record for this urlable_type and urlable_id
		$current_url = $this->model->whereUrlableType($for)->whereUrlableId($for_id)->first();

		$url = $existing ? $existing : false;

		// If we don't have a URL, then create a new one
		if (!$url)
		{
			$url = new UrlModel;
			$url->slug = $slug;			
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

		return true;
	}

	private function updateSubTree($slug, $new_slug)
	{
		$this->model->where('slug', 'like', $slug . '/%')->update(['slug' => \DB::raw("REPLACE(slug, '" . $slug . "', '" . $new_slug . "')")]);
	}

	public function delete($for, $for_id)
	{
		$url = $this->model->whereUrlableType($for)->whereUrlableId($for_id)->first();

		if ($url)
		{
			$url->delete();
		}
	}

	public function canUse($slug, $for, $for_id)
	{
		if (!$this->slugifier->validate($slug))
		{
			throw new InvalidSlug('The slug is not valid');
		}

		// do we already have a record for this slug?
		$existing = UrlModel::whereSlug($slug)->first();

		if ($existing)
		{
			// If the existing matches the type and id, then we can use it
			if ($existing->urlable_type == $for && $existing->urlable_id == $for_id)
			{
				return true;
			}

			// If the exisitng type is a url, then it can be overwritten (otherwise this would be 'reserved' forever)
			if ($existing->urlable_type == 'redirect')
			{
				return true;
			}

			throw new UrlAlreadyExists('The requested URL is already in use.');
		}
	}
}