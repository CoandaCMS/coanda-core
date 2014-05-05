<?php namespace CoandaCMS\Coanda\Layout\Repositories;

interface LayoutBlockRepositoryInterface {

	// public function getBlocksForRegion($layout, $region_identifier, $module_identifier, $module_id);

	// public function defaultBlocksForRegion($layout, $region_identifier);

	public function getBlockList($per_page);

	public function getBlockVersion($block_id, $version);

	public function createNewBlock($type);

	public function saveDraftBlockVersion($version, $data);

	public function discardDraftBlock($version);

	public function deleteBlock($block_id);

}
