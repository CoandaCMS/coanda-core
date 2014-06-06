<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda, App;
use Carbon\Carbon;

class Page extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Pages\Repositories\Eloquent\Presenters\Page';

    /**
     * @var array
     */
    protected $fillable = ['is_home', 'type', 'created_by', 'edited_by', 'current_version'];
    
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
     * @return mixed
     */
    public function locations()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageLocation');
	}

	/**
	 * Get the versions for this page
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function versions()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion');
	}

    /**
     * @param $version
     * @return mixed
     */
    public function getVersion($version)
	{
		return $this->versions()->whereVersion($version)->first();
	}

	/**
	 * Get the page type for this page
	 * @return CoandaCMS\Coanda\Pages\PageTypeInterface [description]
	 */
	public function pageType()
	{
		if (!$this->pageType)
		{
			if ($this->is_home)
			{
				$this->pageType = Coanda::module('pages')->getHomePageType($this->type);
			}
			else
			{
				$this->pageType = Coanda::module('pages')->getPageType($this->type);
			}
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

    /**
     * @return bool
     */
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

    /**
     * @return bool|Carbon
     */
    public function getVisibleFromAttribute()
	{
		$date = $this->currentVersion()->visible_from;

		if ($date && $date !== '')
		{
			return new Carbon($date, date_default_timezone_get());
		}

		return false;
	}

    /**
     * @return bool|Carbon
     */
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
		return $this->pageType()->showMeta();
	}

	public function setRemoteId($remote_id)
	{
		$this->remote_id = $remote_id;
		$this->save();
	}
}