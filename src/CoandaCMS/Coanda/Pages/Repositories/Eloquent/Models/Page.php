<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda, App;
use Carbon\Carbon;

/**
 * Class Page
 * @package CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models
 */
class Page extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Pages\Presenters\Page';

    /**
     * @var
     */
    private $pageType;
    /**
     * @var
     */
    private $slug;
    /**
     * @var
     */
    private $currentVersion;
    /**
     * @var
     */
    private $parents;
    /**
     * @var
     */
    private $children;
    /**
     * @var
     */
    private $subTreeCount;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pages';

    /**
     *
     */
    public function delete()
	{
		foreach ($this->versions() as $version)
		{
			$version->delete();
		}

		parent::delete();
	}

	/**
	 * Get the versions for this page
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function versions()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion');
	}

	public function getVersion($version)
	{
		return $this->versions()->whereVersion($version)->first();
	}

	/**
	 * Returns the parent page for this page
	 * @return CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page [description]
	 */
	public function parent()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page', 'parent_page_id');
	}

	/**
	 * Returns all the children of this page
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function children()
	{
		if (!$this->children)
		{
			if ($this->is_trashed)
			{
				$this->children = $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page', 'parent_page_id')->orderBy('order', 'asc');
			}
			else
			{
				$this->children = $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page', 'parent_page_id')->orderBy('order', 'asc')->whereIsTrashed(0);	
			}
		}
		
		return $this->children;
	}

    /**
     * @return mixed
     */
    public function subTreeCount()
	{
		if (!$this->subTreeCount)
		{
			$path = $this->path == '' ? '/' : $this->path;

			$this->subTreeCount = Page::where('path', 'like', $path . $this->id . '/%')->count();
		}

		return $this->subTreeCount;
	}

    /**
     * @return array
     */
    public function pathArray()
	{
		return explode('/', $this->path);
	}

    /**
     * @return int
     */
    public function depth()
	{
		return count($this->pathArray());
	}

    /**
     * @return int
     */
    public function getDepthAttribute()
	{
		return $this->depth();
	}

	/**
	 * Loop through the path and build up the collection of parents
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function parents()
	{
		if (!$this->parents)
		{
			$this->parents = new \Illuminate\Database\Eloquent\Collection;

			foreach ($this->pathArray() as $parent_id)
			{
				$parent = $this->find($parent_id);

				if ($parent)
				{
					$this->parents->add($parent);					
				}
			}
		}

		return $this->parents;
	}

	/**
	 * Get the page type for this page
	 * @return CoandaCMS\Coanda\Pages\PageTypeInterface [description]
	 */
	public function pageType()
	{
		if (!$this->pageType)
		{
			$this->pageType = Coanda::module('pages')->getPageType($this->type);
		}

		return $this->pageType;
	}

	/**
	 * Gets the name of the type
	 * @return srting
	 */
	public function typeName()
	{
		return $this->pageType()->name;
	}

	/**
	 * Calls the typeName method
	 * @return string
	 */
	public function getTypeNameAttribute()
	{
		return $this->typeName();
	}

	/**
	 * Returns the current version object for this page
	 * @return CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion
	 */
	public function currentVersion()
	{
		if (!$this->currentVersion)
		{
			$this->currentVersion = $this->versions()->whereVersion($this->current_version)->first();
		}

		return $this->currentVersion;
	}

	/**
	 * Get the status of the current version
	 * @return string
	 */
	public function getStatusAttribute()
	{
		return $this->currentVersion()->status;
	}

	public function getIsVisibleAttribute()
	{
		$from = $this->getVisibleFromAttribute();
		$to = $this->getVisibleToAttribute();

		if (!$from && !$to)
		{
			return true;
		}

		$now = Carbon::now(date_default_timezone_get());

		$is_visible = false;

		// Do we have a from and a to date?
		if ($from && $to)
		{
			if ($now->gt($from) && $now->lt($to))
			{
				$is_visible = true;
			}
		}
		else
		{
			// We have a from date
			if ($from)
			{
				if ($now->gt($from))
				{
					$is_visible = true;
				}
			}

			if ($to)
			{
				if ($now->lt($to))
				{
					$is_visible = true;
				}
			}
		}

		return $is_visible;
	}

	public function getVisibleFromAttribute()
	{
		$date = $this->currentVersion()->visible_from;

		if ($date && $date !== '')
		{
			return new Carbon($date, date_default_timezone_get());
		}

		return false;
	}

	public function getVisibleToAttribute()
	{
		$date = $this->currentVersion()->visible_to;

		if ($date && $date !== '')
		{
			return new Carbon($date, date_default_timezone_get());
		}

		return false;
	}

	/**
	 * Return all the attributes for the current version
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getAttributesAttribute()
	{
		return $this->currentVersion()->attributes()->get();
	}

	/**
	 * Returns the slug for this page
	 * @return CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models\Url
	 */
	public function getSlugAttribute()
	{
		if (!$this->slug)
		{
			$urlRepository = App::make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');

			try
			{
				$this->slug = $urlRepository->findFor('page', $this->id)->slug;
			}
			catch(\CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound $exception)
			{
				$this->slug = '';
			}
		}

		return $this->slug;
	}

	/**
	 * Check if the status of the current version is a draft
	 * @return boolean
	 */
	public function getIsDraftAttribute()
	{
		return $this->currentVersion()->status == 'draft';
	}

	/**
	 * Check if the status of the current version is pending
	 * @return boolean
	 */
	public function getIsPendingAttribute()
	{
		return $this->currentVersion()->status == 'pending';
	}

    /**
     * @return mixed
     */
    public function getShowMetaAttribute()
	{
		$type = $this->pageType();

		return $type->showMeta();
	}
}