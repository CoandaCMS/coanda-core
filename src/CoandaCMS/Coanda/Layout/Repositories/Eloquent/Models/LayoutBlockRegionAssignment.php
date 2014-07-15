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

	public function block()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlock', 'block_id');
	}

	public function forLayoutRegion($layout_identifier, $region_identifier, $module_identifier, $cascade = false)
	{
		$query = $this->where('layout_identifier', '=', $layout_identifier)
					->where('region_identifier', '=', $region_identifier)
					->where('module_identifier', '=', $module_identifier);

		if ($module_identifier !== 'default')
		{
			$query->where('cascade', '=', $cascade);
		}

		$query->orderBy('order', 'asc');

		return $query->lists('block_id');
	}
}