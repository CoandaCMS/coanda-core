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
    private $currentVersion;

    private $cachedAttributes;

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

	public function firstLocation()
	{
		return $this->locations()->first();
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

	private function getPageTypeDefinition()
	{
		$pageType = $this->pageType();
		
		return $pageType->attributes();
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
		return $this->attributes();
	}

	public function attributes()
	{
		return $this->currentVersion()->attributes()->with('version')->get();
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

	public function getIsHiddenAttribute()
	{
		return $this->currentVersion()->is_hidden;
	}

	public function getIsHiddenNavigationAttribute()
	{
		return $this->currentVersion()->is_hidden_navigation;
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

	public function availableTemplates()
	{
		return $this->pageType()->availableTemplates();
	}

	public function renderAttributes($location)
	{
		if (!$this->cachedAttributes)
		{
			$this->cachedAttributes = new \stdClass;

			$this->renderCurrentAttributes($this->cachedAttributes, $location);

			$this->addMissingAttributes($this->cachedAttributes);
		}
        
        return $this->cachedAttributes;
	}

	private function renderCurrentAttributes($attributes, $location)
	{
		foreach ($this->attributes() as $attribute)
		{
			$attributes->{$attribute->identifier} = $attribute->render($this, $location);
		}

		return $attributes;
	}

	private function addMissingAttributes($attributes)
	{		
		// Add any attributes which are on the definition, but not in the object..
		$attribute_definition_list = $this->getPageTypeDefinition();

		foreach ($attribute_definition_list as $attribute_definition_identfier => $attribute_definition)
		{
			if (!property_exists($this->cachedAttributes, $attribute_definition_identfier))
			{
				$this->cachedAttributes->{$attribute_definition_identfier} = isset($attribute_definition['default']) ? $attribute_definition['default'] : '';
			}
		}			
	}


}