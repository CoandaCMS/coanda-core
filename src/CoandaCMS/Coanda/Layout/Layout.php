<?php namespace CoandaCMS\Coanda\Layout;

use Coanda, App, View;

/**
 * Class Layout
 * @package CoandaCMS\Coanda\Layout
 */
abstract class Layout {

    /**
     * @var
     */
    private $blocks;
    /**
     * @var
     */
    private $block_repository;

    /**
     * @return mixed
     */
    abstract public function identifier();

    /**
     * @return mixed
     */
    abstract public function template();

    /**
     * @return mixed
     */
    abstract public function name();

    /**
     * @return array
     */
    public function regions()
	{
		// TODO - allowed block types for the region.
		// TODO - maxium block count for the region - e.g. they can only add one block of a certain type in this region
		return [];
	}

    /**
     * @return array
     */
    public function pageTypes()
	{
		return [];
	}

    /**
     * @return int
     */
    public function regionCount()
	{
		return count($this->regions());
	}

    /**
     * @param $region_identifier
     * @return bool
     */
    public function region($region_identifier)
	{
		if (array_key_exists($region_identifier, $this->regions()))
		{
			return $this->regions()[$region_identifier];
		}

		return false;		
	}

    /**
     * @return mixed
     */
    private function blockRepository()
	{
		if (!$this->block_repository)
		{
			$this->block_repository = App::make('CoandaCMS\Coanda\Layout\Repositories\LayoutBlockRepositoryInterface');	
		}

		return $this->block_repository;	
	}

    /**
     * @param $region
     * @return mixed
     */
    public function defaultBlocks($region)
	{
		return $this->blockRepository()->defaultBlocksForRegion($this->identifier(), $region);
	}

    /**
     * @param $region
     * @return mixed
     */
    public function defaultBlockCount($region)
	{
		return $this->defaultBlocks($region)->count();
	}

    /**
     * @param $region
     * @param $module
     * @param $module_identifier
     * @return $this
     */
    public function blocks($region, $module, $module_identifier)
	{
		$blocks = $this->blockRepository()->blocksForRegionAndModule($this->identifier(), $region, $module, $module_identifier);

		// If we have not got any blocks, then fetch the default ones...
		if (count($blocks) == 0)
		{
			$blocks = $this->defaultBlocks($region);
		}

		$this->blocks = $blocks;

		return $this;
	}

    /**
     * @param $region
     * @param $module
     * @param $module_identifier
     * @return mixed
     */
    public function regionBlocks($region, $module, $module_identifier)
	{
		return $this->blockRepository()->regionBlocks($this->identifier(), $region, $module, $module_identifier);
	}

    /**
     * @return $this
     * @throws \Exception
     */
    public function get()
	{
		if ($this->blocks)
		{
			return $this->blocks();
		}

		throw new \Exception('Please call the blocks() method prior to calling get().');
	}

    /**
     * @return string
     * @throws \Exception
     */
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
