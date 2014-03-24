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
		$url = $this->model->whereSlug($slug)->first();

		if (!$url)
		{
			throw new UrlNotFound('Url for /' . $slug . ' not found');
		}
		
		return $url;
	}

	public function register($slug, $for, $for_id)
	{
		// Is this a valid slug?
		if (!$this->slugifier->validate($slug))
		{
			throw new InvalidSlug('The requested slug is not valid');
		}

		// do we already have a record for this slug?
		$existing = UrlModel::whereSlug($slug)->first();

		if ($existing)
		{
			// If the existing url matches the type and id, then we don't need to do anything..
			if ($existing->urlable_type == $for && $existing->urlable_id == $for_id)
			{
				return true;
			}

			// If the existing one is a url, then we can overwrite it, otherwise it is alreay taken.
			if ($existing->urlable_type !== 'url')
			{
				throw new UrlAlreadyExists('The requested URL is already in use.');
			}
		}

		// Do we have a record for this urlable_type and urlable_id - if so lets redirect that to this new one
		$existing_for_type = UrlModel::whereUrlableType($for)->whereUrlableId($for_id)->first();

		if ($existing)
		{
			$existing->urlable_type = $for;
			$existing->urlable_id = $for_id;

			$existing->save();
		}
		else
		{
			$new_url = new UrlModel;
			$new_url->slug = $slug;
			$new_url->urlable_type = $for;
			$new_url->urlable_id = $for_id;

			$new_url->save();
		}

		// If we have an existing url, then set it as a 'redirect' to the new url object
		if ($existing_for_type)
		{
			$existing_for_type->urlable_type = 'url';
			$existing_for_type->urlable_id = $existing ? $existing->id : $new_url->id;
			$existing_for_type->save();
		}

		return true;
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
			if ($existing->urlable_type == 'url')
			{
				return true;
			}

			throw new UrlAlreadyExists('The requested URL is already in use.');
		}
	}

	public function getForPage($id)
	{
		$url = UrlModel::whereUrlableType('page')->whereUrlableId($id)->first();

		return $url->slug;
	}

}