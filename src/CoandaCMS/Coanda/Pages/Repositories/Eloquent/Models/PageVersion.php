<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda;

class PageVersion extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

	protected $presenter = 'CoandaCMS\Coanda\Pages\Presenters\PageVersion';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pageversions';

	public function save(array $options = [])
	{
		if (!$this->preview_key)
		{
			$this->preview_key = md5($this->page_id . '-' . $this->version . '-' . time());
		}

		parent::save($options);
	}

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
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute');
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

}