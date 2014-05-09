<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Coanda;

/**
 * Class LayoutBlockAttribute
 * @package CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models
 */
class LayoutBlockAttribute extends \Illuminate\Database\Eloquent\Model {

    /**
     * @var string
     */
    protected $table = 'layoutblockattributes';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function version()
    {
    	return $this->belongsTo('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlockVersion', 'layout_block_version_id');
    }

    /**
     * @return mixed
     */
    public function block()
	{
		return $this->version->block;
	}

    /**
     * @return mixed
     */
    public function type()
	{
		return Coanda::getAttributeType($this->type);
	}

    /**
     * @return mixed
     */
    public function blockType()
	{
		return $this->block()->blockType();
	}

    /**
     * @return mixed
     */
    public function name()
	{
		$attributes = $this->block()->blockType()->attributes();

		return isset($attributes[$this->identifier]) ? $attributes[$this->identifier]['name'] : $this->identifier;
	}

    /**
     * @return mixed
     */
    public function getNameAttribute()
	{
		return $this->name();
	}

    /**
     * @return mixed
     */
    public function typeData()
	{
		// Let the type do whatever with the attribute to return the data required...
		return $this->type()->data($this->attribute_data);
	}

    /**
     * @return mixed
     */
    public function getTypeDataAttribute()
	{
		return $this->typeData();
	}

    /**
     * @return bool
     */
    public function isRequired()
	{
		$attributes = $this->blockType()->attributes();

		return isset($attributes[$this->identifier]['required']) ? $attributes[$this->identifier]['required'] : false;
	}

    /**
     * @return bool
     */
    public function getIsRequiredAttribute()
	{
		return $this->isRequired();
	}

    /**
     * @param $data
     */
    public function store($data)
	{
		// Let the type class validate/manipulate the data...
		$this->attribute_data = $this->type()->store($data, $this->isRequired(), $this->name());

		$this->save();
	}

}