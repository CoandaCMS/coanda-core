<?php namespace CoandaCMS\Coanda\Layout\Repositories;

interface LayoutRepositoryInterface {

    public function getBlock($id);

    public function getBlocks($layout_identifier, $region_identifier, $module_identifier, $cascade);
}
