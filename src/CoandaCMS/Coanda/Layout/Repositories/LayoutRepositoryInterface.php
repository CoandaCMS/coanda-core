<?php namespace CoandaCMS\Coanda\Layout\Repositories;

interface LayoutRepositoryInterface {

    public function getBlock($id);

    public function getBlocksForLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade);

    public function getPaginatedBlocks($per_page);

    public function addBlock($type, $data);

    public function updateBlock($block, $data);

    public function addRegionAssignment($block_id, $layout_identifier, $region_identifier, $module_identifier, $cascase);

    public function removeAssignmentBlock($assignment_id);

    public function getModuleIdentifiersForRegion($layout_identifier, $region_identifier);

    public function getAssignmentsByModuleIdentifier($layout_identifier, $region_identifier, $module_identifier, $per_page);

    public function updateAssignmentOrders($layout_identifier, $region_identifier, $module_identifier, $new_ordering);
}
