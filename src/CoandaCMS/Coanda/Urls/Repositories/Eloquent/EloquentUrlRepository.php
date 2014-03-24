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

	public function register($slug, $urlable_type, $urlable_id)
	{
		// do we already have a record for this slug?
		$existing = UrlModel::whereSlug($slug)->first();

		if ($existing)
		{
			throw new UrlAlreadyExists('The requested URL is already in use.');
		}

		// Is this a valid slug?
		if (!$this->slugifier->validate($slug))
		{
			throw new InvalidSlug('The requested slug is not valid');
		}

		$new_url = new UrlModel;
		$new_url->slug = $slug;
		$new_url->urlable_type = $urlable_type;
		$new_url->urlable_id = $urlable_id;

		$new_url->save();

		return $new_url;
	}

	public function canUse($slug)
	{
		if (!$this->slugifier->validate($slug))
		{
			throw new InvalidSlug('The slug is not valid');
		}

		// do we already have a record for this slug?
		$existing = UrlModel::whereSlug($slug)->first();

		if ($existing)
		{
			throw new UrlAlreadyExists('The requested URL is already in use.');
		}
	}

}