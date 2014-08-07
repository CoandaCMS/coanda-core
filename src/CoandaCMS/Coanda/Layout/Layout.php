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

    public function render($data)
    {
        $template = $this->template();

        $data = $this->preRender($data);

        return View::make($template, $data)->render();
    }

    public function preRender($data)
    {
        return $data;
    }
}
