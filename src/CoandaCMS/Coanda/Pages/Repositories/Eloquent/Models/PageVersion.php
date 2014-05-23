<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda;

/**
 * Class PageVersion
 * @package CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models
 */
class PageVersion extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var array
     */
    protected $dates = ['visible_from', 'visible_to'];

    /**
     * @var array
     */
    protected $fillable = ['page_id', 'version', 'status', 'created_by', 'edited_by', 'meta_page_title', 'meta_description', 'visible_from', 'visible_to', 'layout_identifier'];

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Pages\Presenters\PageVersion';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pageversions';

    /**
     * @param array $options
     */
    public function save(array $options = [])
	{
		if (!$this->preview_key)
		{
			$this->preview_key = md5($this->page_id . '-' . $this->version . '-' . time());
		}

		parent::save($options);
	}

    /**
     *
     */
    public function delete()
	{
		foreach ($this->attributes()->get() as $attribute)
		{
			$attribute->delete();
		}

		foreach ($this->slugs as $slug)
		{
			$slug->delete();
		}

		parent::delete();
	}

	/**
	 * Returns all the attributes for the page
	 * @return
	 */
	public function attributes()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute')->orderBy('order', 'asc');
	}

    /**
     * @return mixed
     */
    public function slugs()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersionSlug', 'version_id');
	}

    /**
     * @return mixed
     */
    public function comments()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersionComment', 'version_id')->orderBy('created_at', 'desc');;
	}

    /**
     * @param $page_location_id
     * @return string
     */
    public function slugForLocation($page_location_id)
	{
		$slug = $this->slugs()->wherePageLocationId($page_location_id)->first();

		if ($slug)
		{
			return $slug->slug;
		}

		return '';
	}

	/**
	 * Returns a specific attribute by the identifier
	 * @param  string $identifier The attribute you would like
	 * @return
	 */
	public function getAttributeByIdentifier($identifier)
	{
		return $this->attributes()->whereIdentifier($identifier)->first();
	}

	/**
	 * Gets the page for this version
	 * @return CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page The page
	 */
	public function page()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page');
	}

    /**
     *
     */
    public function checkAttributes()
	{
		$pageType = $this->page->pageType();
		$attribute_definition_list = $pageType->attributes();

		$existing_attributes = [];

		// Do all the existing attributes still exist on the definition?
		foreach ($this->attributes()->get() as $attribute)
		{
			if (!array_key_exists($attribute->identifier, $attribute_definition_list))
			{
				// This attribute is no longer in the definition, so lets remove it
				$attribute->delete();
			}
			else
			{
				$existing_attributes[$attribute->identifier] = $attribute;
			}
		}

		$index = 1;

		foreach ($attribute_definition_list as $attribute_identifier => $definition)
		{
			if (!in_array($attribute_identifier, array_keys($existing_attributes)))
			{
				// We need to add this new attribute
				$page_attribute_type = Coanda::getAttributeType($definition['type']);

				$new_attribute = new \CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute;

				$new_attribute->type = $page_attribute_type->identifier();
				$new_attribute->identifier = $definition['identifier'];
				$new_attribute->order = $index;

				$this->attributes()->save($new_attribute);
			}
			else
			{
				// Update the order on the existing attribute (in case we have added another one)
				$existing_attributes[$attribute_identifier]->order = $index;
				$existing_attributes[$attribute_identifier]->save();				
			}

			$index ++;
		}
	}

    /**
     * @return bool
     */
    public function layout()
    {
        if ($this->layout_identifier !== '')
        {
            return Coanda::module('layout')->layoutByIdentifier($this->layout_identifier);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getLayoutAttribute()
    {
        return $this->layout();
    }

    /**
     * @param $region
     * @return array
     */
    public function layoutRegionBlocks($region)
    {
    	$layout = $this->layout();

    	if ($layout)
    	{
    		return $layout->regionBlocks($region, 'pages', $this->page->id . ':' . $this->version);
    	}

    	return [];
    }
}