<?php namespace CoandaCMS\Coanda\Layout;

use Coanda, App, View;

/**
 * Class Layout
 * @package CoandaCMS\Coanda\Layout
 */
abstract class Layout {

    /**
     * @return string
     */
    abstract public function identifier();

    /**
     * @return string
     */
    abstract public function template();

    /**
     * @return string
     */
    abstract public function name();

    /**
     * @return array
     */
    public function pageTypes()
	{
		return [];
	}

    /**
     * @return array
     */
    public function regions()
    {
        return [];
    }

    public function render($data)
    {
        $template = $this->template();

        $data['blocks'] = $this->getBlocks($data);

        return View::make($template, $data)->render();
    }

    private function getBlocks($data)
    {
        $blocks = new \stdClass;

        foreach ($this->regions() as $region_identifier => $region)
        {
            $blocks->{$region_identifier} = $this->getBlocksForRegion($region_identifier, $data);
        }

        return $blocks;
    }

    private function getBlocksForRegion($region_identifier, $data)
    {
        $module_identifier = $data['module'] . ':' . $data['module_identifier'];

        $blocks = Coanda::layout()->getBlocks($this->identifier(), $region_identifier, $module_identifier);

        // If we have no blocks for this specific identifier, then look through the breadcrumb
        if ($blocks->count() == 0)
        {
            if (isset($data['breadcrumb']) && count($data['breadcrumb']) > 0)
            {
                $breadcrumb = $data['breadcrumb'];

                array_pop($breadcrumb);

                foreach (array_reverse($breadcrumb) as $breadcrumb_item)
                {
                    if (isset($breadcrumb_item['layout_identifier']))
                    {
                        echo '<pre>';
                        var_export($breadcrumb_item['layout_identifier']);
                        echo '</pre>';

                        $blocks = Coanda::layout()->getBlocks($this->identifier(), $region_identifier, $breadcrumb_item['layout_identifier']);

                        if ($blocks->count() > 0)
                        {
                            break;
                        }
                    }
                }
            }
        }

        // If we still have no blocks, then see if there are any defaults
        if ($blocks->count() == 0)
        {
            $blocks = Coanda::layout()->getDefaultBlocks($this->identifier(), $region_identifier);
        }

        return $blocks;
    }
}
