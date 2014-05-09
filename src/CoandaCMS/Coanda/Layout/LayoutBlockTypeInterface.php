<?php namespace CoandaCMS\Coanda\Layout;

/**
 * Interface LayoutBlockTypeInterface
 * @package CoandaCMS\Coanda\Layout
 */
interface LayoutBlockTypeInterface {

    /**
     * @return mixed
     */
    public function name();

    /**
     * @return mixed
     */
    public function identifier();

    /**
     * @return mixed
     */
    public function template();

    /**
     * @return mixed
     */
    public function attributes();

    /**
     * @param $block
     * @return mixed
     */
    public function generateName($block);

}