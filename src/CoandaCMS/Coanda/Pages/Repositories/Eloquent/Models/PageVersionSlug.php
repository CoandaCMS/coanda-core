<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda, App;
use Carbon\Carbon;

class PageVersionSlug extends Eloquent {

	protected $table = 'pageversionslugs';

	protected $fillable = ['version_id', 'page_location_id', 'slug'];

	public function version()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion');
	}

	public function location()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageLocation', 'page_location_id');
	}

	public function getFullSlugAttribute()
	{
		$base_slug = $this->base_slug;

		if ($base_slug !== '')
		{
			$base_slug .= '/';
		}

		return $base_slug . $this->slug;
	}

	public function baseSlug()
	{
		$location = $this->location;

		if ($location)
		{
			return $location->slug;
		}

		return '';
	}

	public function getBaseSlugAttribute()
	{
		return $this->baseSlug();
	}
}