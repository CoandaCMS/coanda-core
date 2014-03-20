<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda;

class PageAttribute extends Eloquent {

	public $timestamps = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pageattributes';

	public function version()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion', 'page_version_id');
	}

	public function page()
	{
		return $this->version->page;
	}

	public function pageType()
	{
		return $this->page()->pageType();
	}

	public function name()
	{
		return $this->pageType()->attributes()[$this->identifier]['name'];
	}

	public function isRequired()
	{
		return $this->pageType()->attributes()[$this->identifier]['required'];
	}

	public function getIsRequiredAttribute()
	{
		return $this->isRequired();
	}

	public function getNameAttribute()
	{
		return $this->name();
	}

	public function type()
	{
		return Coanda::getPageAttributeType($this->type);
	}

	public function typeData()
	{
		// Let the type do whatever with the attribute to return the data required...
		return $this->type()->data($this);
	}

	public function getTypeDataAttribute()
	{
		return $this->typeData();
	}

	public function store($data)
	{
		// Let the type class validate/manipulate/store the data..
		$this->type()->store($this, $data);
	}

}