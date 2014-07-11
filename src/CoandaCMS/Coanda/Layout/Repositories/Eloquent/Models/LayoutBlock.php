<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Eloquent, DB, Coanda;

class LayoutBlock extends Eloquent {

	protected $table = 'layoutblocks';
	protected $region_link_table = 'layoutblockregions';

	protected $fillable = [
		'name',
		'type',
		'block_data'
	];

	private $cached_attributes;

	private function blockType()
	{
		return Coanda::layout()->blockType($this->type);
	}

	public function getBlockTypeAttribute()
	{
		return $this->blockType();
	}

	public function attributes()
	{
		if (!$this->cached_attributes)
		{
			$definition = $this->blockType()->blueprint();
			$data = json_decode($this->block_data, true);

			if (!is_array($data))
			{
				$data = [];
			}

			foreach ($data as $identifier => $attribute_data)
			{
				$attribute_type = Coanda::getAttributeType($attribute_data['type_identifier']);


				$this->cached_attributes[$identifier] = new \stdClass;
				$this->cached_attributes[$identifier]->type = $attribute_type;
				$this->cached_attributes[$identifier]->name = $definition[$identifier]['name'];
				$this->cached_attributes[$identifier]->definition = $definition[$identifier];
				$this->cached_attributes[$identifier]->data = $attribute_type->data($attribute_data['content']);
			}
		}

		return $this->cached_attributes;
	}

	public function getAttributesAttribute()
	{
		return $this->attributes();
	}

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

	public function regionAssignmentsPaginated($per_page)
	{
		return DB::table($this->region_link_table)->where('block_id', '=', $this->id)->paginate($per_page);
	}

	public function addRegionAssignment($layout_identifier, $region_identifier, $module_identifier)
	{
		$data = [
			'block_id' => $this->id,
			'layout_identifier' => $layout_identifier,
			'region_identifier' => $region_identifier,
			'module_identifier' => ($module_identifier == '') ? 'default' : $module_identifier
		];
		
		DB::table($this->region_link_table)->insert($data);
	}
}