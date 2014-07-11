<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent;

use CoandaCMS\Coanda\Layout\Repositories\LayoutRepositoryInterface;

use CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlock as BlockModel;

use CoandaCMS\Coanda\Exceptions\AttributeValidationException;
use CoandaCMS\Coanda\Exceptions\ValidationException;

class EloquentLayoutRepository implements LayoutRepositoryInterface {

    private $block_model;

    public function __construct(BlockModel $block_model)
	{
		$this->block_model = $block_model;
	}

    public function getBlock($id)
    {
    	return $this->block_model->find($id);
    }

    public function getBlocksForLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade = false)
    {
        return $this->block_model->forLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade);
    }

    public function getPaginatedBlocks($per_page)
    {
        return $this->block_model->paginate($per_page);
    }

    public function addBlock($type, $data)
    {
        $failed = [];

        if (!isset($data['name']) || $data['name'] == '')
        {
            $failed['name'] = 'Please enter a name';
        }

        foreach ($type->attributes() as $attribute)
        {
            try
            {
                $attribute_data[$attribute->identifier] = [
                    'type_identifier' => $attribute->type->identifier(),
                    'content' => $attribute->type->store(isset($data['attributes'][$attribute->identifier]) ? $data['attributes'][$attribute->identifier] : null, $attribute->required, $attribute->name, ['data_key' => 'attribute_' . $attribute->identifier])
                ];
            }
            catch (AttributeValidationException $exception)
            {
                $failed['attributes'][$attribute->identifier] = $exception->getMessage();
            }
        }

        if (count($failed) > 0)
        {
            throw new ValidationException($failed);
        }

        return $this->block_model->create([
                'name' => $data['name'],
                'type' => $type->identifier(),
                'block_data' => json_encode($attribute_data)
            ]);
    }
}