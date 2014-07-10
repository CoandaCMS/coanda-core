<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Eloquent, DB;

class LayoutBlock extends Eloquent {
	
	protected $table = 'layoutblocks';
	protected $region_link_table = 'layoutblockregions';

	public function forLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade = false)
	{
		// $block_ids = DB::table($this->region_link_table)
		// 			->where('layout_identifier', '=', $layout_identifier)
		// 			->where('region_identifier', '=', $region_identifier)
		// 			->where('module_identifier', '=', $module_identifier)
		// 			->where('cascade', '=', $cascade)
		// 			->lists('block_id');

		// if (count($block_ids) > 0)
		// {
		// 	echo '<pre>';
		// 	var_export($block_ids);
		// 	echo '</pre>';
		// }

		return new \Illuminate\Support\Collection;
	}

}