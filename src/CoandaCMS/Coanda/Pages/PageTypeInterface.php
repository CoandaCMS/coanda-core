<?php namespace CoandaCMS\Coanda\Pages;

/**
 * Interface PageTypeInterface
 * @package CoandaCMS\Coanda\Pages
 */
/**
 * Interface PageTypeInterface
 * @package CoandaCMS\Coanda\Pages
 */
interface PageTypeInterface {

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
    public function icon();

    /**
     * @return mixed
     */
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