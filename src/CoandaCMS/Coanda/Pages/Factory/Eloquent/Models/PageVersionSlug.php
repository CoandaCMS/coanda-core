<?php namespace CoandaCMS\Coanda\Pages\Factory\Eloquent\Models;

use Eloquent, Coanda, App;
use Carbon\Carbon;

use CoandaCMS\Coanda\Pages\Factory\Eloquent\Models\PageLocation as PageLocationModel;

/**
 * Class PageVersionSlug
 * @package CoandaCMS\Coanda\Pages\Factory\Eloquent\Models
 */
class PageVersionSlug extends Eloquent {

    /**
     * @var string
     */
    protected $table = 'pageversionslugs';

    /**
     * @var array
     */
    protected $fillable = ['version_id', 'page_location_id', 'slug'];

    /**
     * @return mixed
     */
    public function version()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Factory\Eloquent\Models\PageVersion');
	}

    /**
     * @return mixed
     */
    public function location()
	{
		return PageLocationModel::wherePageId($this->version->page->id)->whereParentPageId($this->page_location_id)->first(); 
	}

    /**
     * @return mixed
     */
    public function getLocationAttribute()
	{
		return $this->location();
	}

    /**
     * @return string
     */
    public function getFullSlugAttribute()
	{
		$base_slug = $this->base_slug;

		if ($base_slug !== '')
		{
			$base_slug .= '/';
		}

		return $base_slug . $this->slug;
	}

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getBaseSlugAttribute()
	{
		return $this->baseSlug();
	}
}