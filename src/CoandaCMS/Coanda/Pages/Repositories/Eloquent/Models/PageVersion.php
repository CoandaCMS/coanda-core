<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda;

/**
 * Class PageVersion
 * @package CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models
 */
class PageVersion extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

	protected $dates = ['visible_from', 'visible_to'];

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
	 * Get the base slug for this version
	 * @return string the slug
	 */
	public function getBaseSlugAttribute()
	{
		if ($this->page->parent)
		{
			return $this->page->parent->slug . '/';
		}

		return '';
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
				$page_attribute_type = Coanda::module('pages')->getPageAttributeType($definition['type']);

				$new_attribute = new \CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute;

				$new_attribute->type = $page_attribute_type->identifier;
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

}