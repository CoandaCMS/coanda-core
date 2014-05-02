<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Coanda;

class LayoutBlockVersion extends \Illuminate\Database\Eloquent\Model {

    use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    protected $presenter = 'CoandaCMS\Coanda\Layout\Presenters\LayoutBlockVersion';

    protected $table = 'layoutblockversions';

    public function delete()
	{
		foreach ($this->attributes()->get() as $attribute)
		{
			$attribute->delete();
		}

		parent::delete();
	}

    public function block()
    {
    	return $this->belongsTo('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlock', 'layout_block_id');
    }

    public function attributes()
    {
    	return $this->hasMany('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlockAttribute')->orderBy('order', 'asc');;
    }

    public function checkAttributes()
	{
		$type = $this->block->blockType();		
		$attribute_definition_list = $type->attributes();

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
				$attribute_type = Coanda::getAttributeType($definition['type']);

				$new_attribute = new \CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlockAttribute;

				$new_attribute->type = $attribute_type->identifier();
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

	public function getAttributeByIdentifier($identifier)
	{
		return $this->attributes()->whereIdentifier($identifier)->first();
	}

}