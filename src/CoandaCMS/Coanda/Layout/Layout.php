<?php namespace CoandaCMS\Coanda\Layout;

use Coanda, App, View;

abstract class Layout {

	private $blocks;
	private $block_repository;

	abstract public function identifier();

	abstract public function template();

	abstract public function name();

	public function regions()
	{
		// TODO - allowed block types for the region.
		// TODO - maxium block count for the region - e.g. they can only add one block of a certain type in this region
		return [];
	}

	public function pageTypes()
	{
		return [];
	}

	public function regionCount()
	{
		return count($this->regions());
	}

	public function region($region_identifier)
	{
		if (array_key_exists($region_identifier, $this->regions()))
		{
			return $this->regions()[$region_identifier];
		}

		return false;		
	}

	private function blockRepository()
	{
		if (!$this->block_repository)
		{
			$this->block_repository = App::make('CoandaCMS\Coanda\Layout\Repositories\LayoutBlockRepositoryInterface');	
		}

		return $this->block_repository;	
	}

	public function defaultBlocks($region)
	{
		return $this->blockRepository()->defaultBlocksForRegion($this->identifier(), $region);
	}

	public function defaultBlockCount($region)
	{
		return $this->defaultBlocks($region)->count();
	}

	public function blocks($region, $module, $module_identifier)
	{
		// Get the blocks for this region, module and module_identifier combination...
		$blocks = $this->blockRepository()->blocksForRegionAndModule($this->identifier(), $region, $module, $module_identifier);;

		// If we have not got any blocks, then fetch the default ones...
		if (count($blocks) == 0)
		{
			$blocks = $this->defaultBlocks($region);
		}

		$this->blocks = $blocks;

		return $this;
	}

	public function get()
	{
		if ($this->blocks)
		{
			return $this->blocks();
		}

		throw new \Exception('Please call the blocks() method prior to calling get().');
	}

	public function render()
	{
		if ($this->blocks)
		{
			$block_content = '';

			foreach ($this->blocks as $block)
			{
				$block_content .= View::make($block->blockType()->template(), ['block' => $block]);
			}

			return $block_content;
		}

		throw new \Exception('Please call the blocks() method prior to calling render().');
	}
}
