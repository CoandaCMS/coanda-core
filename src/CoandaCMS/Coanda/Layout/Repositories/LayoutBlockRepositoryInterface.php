<?php namespace CoandaCMS\Coanda\Layout\Repositories;

interface LayoutBlockRepositoryInterface {

	public function defaultBlocksForRegion($layout_identifier, $region_identifier);

	public function blocksForRegionAndModule($layout_identifier, $region_identifier, $module, $module_idenfitier);

	public function defaultRegionBlocks($layout_identifier, $region_identifier);

	public function regionBlocks($layout_identifier, $region_identifier, $module, $module_idenfitier);

	public function getBlockList($per_page);

	public function getBlockVersion($block_id, $version);

	public function createNewBlock($type, $layout_identifier, $region_identifier);

	public function saveDraftBlockVersion($version, $data);

	public function discardDraftBlock($version);

	public function deleteBlock($block_id);

	public function addDefaultBlockToRegion($block_id, $region_identifier);

	public function checkBlockIsDefaultInRegion($block_id, $layout_identifier, $region_identifier);

	public function removeDefaultBlockFromRegion($block_id, $layout_identifier, $region_identifier);

	public function updateRegionOrdering($ordering);

	public function addCustomBlockToRegion($block_id, $layout_identifier, $region_identifier, $module, $module_identifier);

}
