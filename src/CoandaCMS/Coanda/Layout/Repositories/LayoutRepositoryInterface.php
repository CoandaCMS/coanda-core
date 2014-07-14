<?php namespace CoandaCMS\Coanda\Layout\Repositories;

interface LayoutRepositoryInterface {

    public function getBlock($id);

    public function getBlocksForLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade);

    public function getPaginatedBlocks($per_page);

    public function addBlock($type, $data);

    public function updateBlock($block, $data);

    public function addRegionAssignment($block_id, $layout_identifier, $region_identifier, $module_identifier, $cascase);
}
