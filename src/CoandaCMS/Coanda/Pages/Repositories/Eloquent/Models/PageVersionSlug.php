<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda, App;
use Carbon\Carbon;

use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageLocation as PageLocationModel;

class PageVersionSlug extends Eloquent {

	protected $table = 'pageversionslugs';

	protected $fillable = ['version_id', 'page_location_id', 'slug'];

	public function version()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion');
	}

	public function location()
	{
		return PageLocationModel::wherePageId($this->version->page->id)->whereParentPageId($this->page_location_id)->first(); 
	}

	public function getLocationAttribute()
	{
		return $this->location();
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
			if ($location->parent)
			{
				return $location->parent->slug;	
			}
		}
		else
		{
			$location = PageLocationModel::whereId($this->page_location_id)->first();

			return $location->slug;
		}

		return '';
	}

	public function getBaseSlugAttribute()
	{
		return $this->baseSlug();
	}
}