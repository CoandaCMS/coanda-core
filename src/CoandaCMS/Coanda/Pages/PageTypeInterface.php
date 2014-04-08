<?php namespace CoandaCMS\Coanda\Pages;

/**
 * Interface PageTypeInterface
 * @package CoandaCMS\Coanda\Pages
 */
interface PageTypeInterface {
	
	// public $name;
	// public $identifier;

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