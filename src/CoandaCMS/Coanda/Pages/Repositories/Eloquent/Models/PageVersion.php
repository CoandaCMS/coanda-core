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

	public function delete()
	{
		foreach ($this->attributes()->get() as $attribute)
		{
			$attribute->delete();
		}

		parent::delete();
	}

	public function attributes()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute');
	}

	public function getAttributeByIdentifier($identifier)
	{
		return $this->attributes()->whereIdentifier($identifier)->first();
	}

	public function page()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page');
	}

	public function getBaseSlugAttribute()
	{
		if ($this->page->parent)
		{
			return $this->page->parent->slug . '/';
		}

		return '';
	}

}