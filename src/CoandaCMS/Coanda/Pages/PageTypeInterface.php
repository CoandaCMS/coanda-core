<?php namespace CoandaCMS\Coanda\Pages;

/**
 * Interface PageTypeInterface
 * @package CoandaCMS\Coanda\Pages
 */
interface PageTypeInterface {
	
    public function name();

    public function identifier();

    public function allowsSubPages();

    /**
     * @return mixed
     */
    public function attributes();

    /**
     * @param $version
     * @return mixed
     */
    public function generateName($version);

    /**
     * @return mixed
     */
    public function showMeta();

}