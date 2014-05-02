<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Coanda;

class LayoutBlockAttribute extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'layoutblockattributes';

    public function version()
    {
    	return $this->belongsTo('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlockVersion', 'layout_block_version_id');
    }

	public function block()
	{
		return $this->version->block;
	}

	public function type()
	{
		return Coanda::getAttributeType($this->type);
	}

	public function blockType()
	{
		return $this->block()->blockType();
	}

	public function name()
	{
		$attributes = $this->block()->blockType()->attributes();

		return isset($attributes[$this->identifier]) ? $attributes[$this->identifier]['name'] : $this->identifier;
	}

	public function getNameAttribute()
	{
		return $this->name();
	}

	public function typeData()
	{
		// Let the type do whatever with the attribute to return the data required...
		return $this->type()->data($this->attribute_data);
	}

	public function getTypeDataAttribute()
	{
		return $this->typeData();
	}

	public function isRequired()
	{
		$attributes = $this->blockType()->attributes();

		return isset($attributes[$this->identifier]['required']) ? $attributes[$this->identifier]['required'] : false;
	}

	public function getIsRequiredAttribute()
	{
		return $this->isRequired();
	}

	public function store($data)
	{
		// Let the type class validate/manipulate the data...
		$this->attribute_data = $this->type()->store($data, $this->isRequired(), $this->name());

		$this->save();
	}

}