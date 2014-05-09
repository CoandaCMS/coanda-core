<?php namespace CoandaCMS\Coanda\Layout\Repositories;

/**
 * Interface LayoutBlockRepositoryInterface
 * @package CoandaCMS\Coanda\Layout\Repositories
 */
interface LayoutBlockRepositoryInterface {

    /**
     * @param $layout_identifier
     * @param $region_identifier
     * @return mixed
     */
    public function defaultBlocksForRegion($layout_identifier, $region_identifier);

    /**
     * @param $layout_identifier
     * @param $region_identifier
     * @param $module
     * @param $module_idenfitier
     * @return mixed
     */
    public function blocksForRegionAndModule($layout_identifier, $region_identifier, $module, $module_idenfitier);

    /**
     * @param $layout_identifier
     * @param $region_identifier
     * @return mixed
     */
    public function defaultRegionBlocks($layout_identifier, $region_identifier);

    /**
     * @param $layout_identifier
     * @param $region_identifier
     * @param $module
     * @param $module_idenfitier
     * @return mixed
     */
    public function regionBlocks($layout_identifier, $region_identifier, $module, $module_idenfitier);

    /**
     * @param $per_page
     * @return mixed
     */
    public function getBlockList($per_page);

    /**
     * @param $block_id
     * @param $version
     * @return mixed
     */
    public function getBlockVersion($block_id, $version);

    /**
     * @param $type
     * @param $layout_identifier
     * @param $region_identifier
     * @return mixed
     */
    public function createNewBlock($type, $layout_identifier, $region_identifier);

    /**
     * @param $version
     * @param $data
     * @return mixed
     */
    public function saveDraftBlockVersion($version, $data);

    /**
     * @param $version
     * @return mixed
     */
    public function discardDraftBlock($version);

    /**
     * @param $block_id
     * @return mixed
     */
    public function deleteBlock($block_id);

    /**
     * @param $block_id
     * @param $region_identifier
     * @return mixed
     */
    public function addDefaultBlockToRegion($block_id, $region_identifier);

    /**
     * @param $block_id
     * @param $layout_identifier
     * @param $region_identifier
     * @return mixed
     */
    public function checkBlockIsDefaultInRegion($block_id, $layout_identifier, $region_identifier);

    /**
     * @param $block_id
     * @param $layout_identifier
     * @param $region_identifier
     * @return mixed
     */
    public function removeDefaultBlockFromRegion($block_id, $layout_identifier, $region_identifier);

    /**
     * @param $ordering
     * @return mixed
     */
    public function updateRegionOrdering($ordering);

    /**
     * @param $block_id
     * @param $layout_identifier
     * @param $region_identifier
     * @param $module
     * @param $module_identifier
     * @return mixed
     */
    public function addCustomBlockToRegion($block_id, $layout_identifier, $region_identifier, $module, $module_identifier);

}
