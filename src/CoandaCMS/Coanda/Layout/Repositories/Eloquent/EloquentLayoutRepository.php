<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent;

use DB;

use CoandaCMS\Coanda\Layout\Repositories\LayoutRepositoryInterface;

use CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlock as BlockModel;
use CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlockRegionAssignment as RegionAssignmentModel;

use CoandaCMS\Coanda\Exceptions\AttributeValidationException;
use CoandaCMS\Coanda\Exceptions\ValidationException;

class EloquentLayoutRepository implements LayoutRepositoryInterface {

    private $block_model;
    private $region_assignment_model;

    public function __construct(BlockModel $block_model, RegionAssignmentModel $region_assignment_model)
	{
		$this->block_model = $block_model;
        $this->region_assignment_model = $region_assignment_model;
	}

    public function getBlock($id)
    {
    	return $this->block_model->find($id);
    }

    public function getBlocksForLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade = false)
    {
        echo '<pre>';
        var_export($layout_identifier);
        var_export($region_identifier);
        var_export($module_identifier);
        var_export($cascade);
        echo '</pre>';

        $blocks = new \Illuminate\Support\Collection;
        $block_ids = $this->region_assignment_model->forLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade);

        if (count($block_ids) > 0)
        {
            foreach ($block_ids as $block_id)
            {
                $block = $this->block_model->find($block_id);

                if ($block)
                {
                    $blocks->push($block);
                }
            }
        }

        return $blocks;
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

    public function updateBlock($block, $data)
    {
        $failed = [];

        if (!isset($data['name']) || $data['name'] == '')
        {
            $failed['name'] = 'Please enter a name';
        }

        foreach ($block->attributes() as $attribute)
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

        $block->name = $data['name'];
        $block->block_data = json_encode($attribute_data);

        $block->save();

        return $block;
    }

    public function addRegionAssignment($block_id, $layout_identifier, $region_identifier, $module_identifier, $cascade)
    {
        $existing_count = $this->region_assignment_model
                    ->where('block_id', '=', $block_id)
                    ->where('layout_identifier', '=', $layout_identifier)
                    ->where('region_identifier', '=', $region_identifier)
                    ->where('module_identifier', '=', $module_identifier)
                    ->where('cascade', '=', $cascade)
                    ->count();

        if ($existing_count == 0)
        {        
            $this->region_assignment_model->create([
                    'block_id' => $block_id,
                    'layout_identifier' => $layout_identifier,
                    'region_identifier' => $region_identifier,
                    'module_identifier' => $module_identifier,
                    'cascade' => $cascade
                ]);
        }
    }

    public function removeAssignmentBlock($assignment_id)
    {
        $assignment = $this->region_assignment_model->find($assignment_id);

        if ($assignment)
        {
            $block_id = $assignment->block_id;

            $assignment->delete();

            return $block_id;
        }
    }

    public function getModuleIdentifiersForRegion($layout_identifier, $region_identifier)
    {
        return $this->region_assignment_model
                    ->select(DB::raw('count(*) as block_count, module_identifier'))
                    ->where('layout_identifier', '=', $layout_identifier)
                    ->where('region_identifier', '=', $region_identifier)
                    ->groupBy('module_identifier')
                    ->get();
    }

    public function getAssignmentsByModuleIdentifier($layout_identifier, $region_identifier, $module_identifier, $per_page)
    {
        return $this->region_assignment_model
            ->where('layout_identifier', '=', $layout_identifier)
            ->where('region_identifier', '=', $region_identifier)
            ->where('module_identifier', '=', $module_identifier)
            ->orderBy('order', 'asc')
            ->paginate($per_page);
    }

    public function updateAssignmentOrders($layout_identifier, $region_identifier, $module_identifier, $new_ordering)
    {
        foreach ($new_ordering as $assignment_id => $new_order)
        {
            $this->region_assignment_model->whereId($assignment_id)->update(['order' => $new_order]);
        }
    }
}