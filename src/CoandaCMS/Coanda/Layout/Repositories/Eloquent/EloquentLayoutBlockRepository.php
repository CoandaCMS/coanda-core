<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent;

use Coanda;

use CoandaCMS\Coanda\Layout\Exceptions\LayoutBlockNotFound;
use CoandaCMS\Coanda\Layout\Exceptions\LayoutBlockVersionNotFound;

use CoandaCMS\Coanda\Exceptions\AttributeValidationException;
use CoandaCMS\Coanda\Exceptions\ValidationException;

use CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlock as LayoutBlockModel;
use CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlockVersion as LayoutBlockVersionModel;
use CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlockAttribute as LayoutBlockAttributeModel;

class EloquentLayoutBlockRepository implements \CoandaCMS\Coanda\Layout\Repositories\LayoutBlockRepositoryInterface {

    private $layout_block_model;
	private $layout_block_version_model;
    private $layout_block_attribute_model;

    public function __construct(LayoutBlockModel $layout_block_model, LayoutBlockVersionModel $layout_block_version_model, LayoutBlockAttributeModel $layout_block_attribute_model)
	{
		$this->layout_block_model = $layout_block_model;
		$this->layout_block_version_model = $layout_block_version_model;
		$this->layout_block_attribute_model = $layout_block_attribute_model;
	}

 //    public function getBlocksForRegion($layout, $region_identifier, $module_identifier, $module_id)
	// {
	// 	dd('getBlocksForRegion');

	// 	$blocks = [];
		
	// 	foreach ($this->layout_block_region_model->whereLayout($layout)->whereRegion($region_identifier)->whereModule($module_identifier)->whereModuleId($module_id)->orderBy('order')->lists('layout_block_id') as $layout_block_id)
	// 	{
	// 		$blocks[] = $this->layout_block_model->find($layout_block_id);
	// 	}

	// 	return $blocks;
	// }

	// public function defaultBlocksForRegion($layout_identifier, $region_identifier)
	// {
	// 	dd('defaultBlocksForRegion');

	// 	$blocks = [];
		
	// 	foreach ($this->layout_block_region_model->whereLayoutIdentifier($layout_identifier)->whereRegion($region_identifier)->whereModule('*')->orderBy('order')->lists('layout_block_id') as $layout_block_id)
	// 	{
	// 		$blocks[] = $this->layout_block_model->find($layout_block_id);
	// 	}

	// 	return $blocks;
	// }

	public function getBlockById($id)
	{
		$block = $this->layout_block_model->find($id);

		if ($block)
		{
			return $block;
		}

		throw new LayoutBlockNotFound('Block #' . $id . ' not found');
	}

	public function getBlockList($per_page)
	{
		return $this->layout_block_model->orderBy('created_at', 'desc')->paginate($per_page);
	}

	public function getBlockVersion($block_id, $version)
	{
		$block = $this->layout_block_model->find($block_id);

		if ($block)
		{
			$version = $block->versions()->whereStatus('draft')->whereVersion($version)->first();

			if ($version)
			{
				// Let the version update/check its attributes against the definition (which might have changed)
				$version->checkAttributes();

				return $version;
			}

			throw new LayoutBlockVersionNotFound;
		}

		throw new LayoutBlockFound;
	}

	public function createNewBlock($type)
	{
		// Create the block
		$block = new $this->layout_block_model;
		$block->current_version = 1;
		$block->type = $type->identifier();
		$block->save();

		// Create the version
		$version = new $this->layout_block_version_model;
		$version->version = 1;
		$version->status = 'draft';

		$block->versions()->save($version);

		// Now add all the attributes
		$index = 1;

		foreach ($type->attributes() as $type_attribute)
		{
			$attribute_type = Coanda::getAttributeType($type_attribute['type']);

			$attribute = new $this->layout_block_attribute_model;

			$attribute->type = $attribute_type->identifier();
			$attribute->identifier = $type_attribute['identifier'];
			$attribute->order = $index;

			$version->attributes()->save($attribute);

			$index ++;
		}

		return $block;
	}

    public function createNewVersion($block_id)
	{
		$block = $this->getBlockById($block_id);

		$type = $block->blockType();

		$current_version = $block->currentVersion();
		$latest_version = $block->versions()->orderBy('version', 'desc')->first();

		$new_version_number = $latest_version->version + 1;

		// Create the version
		$version = new $this->layout_block_version_model;
		$version->version = $new_version_number;
		$version->status = 'draft';

		$block->versions()->save($version);

		// Add all the attributes..
		$index = 1;

		foreach ($type->attributes() as $type_attribute)
		{
			$attribute_type = Coanda::getAttributeType($type_attribute['type']);

			$attribute = new $this->layout_block_attribute_model;

			$attribute->type = $attribute_type->identifier();
			$attribute->identifier = $type_attribute['identifier'];
			$attribute->order = $index;

			// Copy the attribute data from the current version
			$existing_attribute = $current_version->getAttributeByIdentifier($type_attribute['identifier']);

			$attribute->attribute_data = $existing_attribute ? $existing_attribute->attribute_data : '';

			$version->attributes()->save($attribute);

			$index ++;
		}

		return $new_version_number;
	}


	public function saveDraftBlockVersion($version, $data)
	{
		$failed = [];

		foreach ($version->attributes as $attribute)
		{
			try
			{
				$attribute->store($data['attribute_' . $attribute->id]);
			}
			catch (AttributeValidationException $exception)
			{
				$failed['attribute_' . $attribute->id] = $exception->getMessage();
			}
		}

		$version->save();

		if (count($failed) > 0)
		{
			throw new ValidationException($failed);
		}
	}

    public function publishBlockVersion($version)
	{
		$block = $version->block;

		if ((int)$version->version !== 1)
		{
			// set the current published version to be archived
			$block->currentVersion()->status = 'archived';
			$block->currentVersion()->save();			
		}

		// set this version to be published
		$version->status = 'published';
		$version->save();
		
		// update the page name attribute (via the type)
		$block->name = $block->blockType()->generateName($version);
		$block->current_version = $version->version;
		$block->save();
	}

	public function discardDraftBlock($version)
	{
		$block = $version->block;

		$version->delete();

		// If now have no versions, then remove the page too
		if ($block->versions->count() == 0)
		{
			$block->delete();
		}
	}

	public function deleteBlock($block_id)
	{
		$block = $this->getBlockById($block_id);

		$block->delete();
	}
}