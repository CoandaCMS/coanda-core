<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent;

use CoandaCMS\Coanda\Layout\Repositories\LayoutRepositoryInterface;

use CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlock as BlockModel;

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

    public function getBlocks($layout_identifier, $region_identifier, $module_identifier, $cascade = false)
    {
        return $this->block_model->forLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade);
    }
}