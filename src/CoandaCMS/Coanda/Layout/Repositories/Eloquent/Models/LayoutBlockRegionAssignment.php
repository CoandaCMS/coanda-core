<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Eloquent;

class LayoutBlockRegionAssignment extends Eloquent {

	protected $table = 'layoutblockregionassignments';

	protected $fillable = [
		'block_id',
		'layout_identifier',
		'region_identifier',
		'module_identifier',
		'cascade'
	];

	public function forLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade = false)
	{
		$block_ids = $this->where('layout_identifier', '=', $layout_identifier)
					->where('region_identifier', '=', $region_identifier)
					->where('module_identifier', '=', $module_identifier)
					->where('cascade', '=', $cascade)
					->lists('block_id');

		return $block_ids;
	}

}