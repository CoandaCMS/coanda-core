<?php namespace CoandaCMS\Coanda\Pages;

/**
 * Class PageType
 * @package CoandaCMS\Coanda\Pages
 */
abstract class PageType {

    /**
     * @return mixed
     */
    abstract public function name();

    /**
     * @return mixed
     */
    abstract public function identifier();

    /**
     * @return mixed
     */
    abstract public function attributes();

    /**
     * @param $version
     * @return mixed
     */
    abstract public function generateName($version);


    /**
     * @return string
     */
    public function template()
    {
        // Return a sensible default...
        return 'pagetypes.' . $this->identifier();
    }

    /**
     * @return mixed
     */
    public function icon()
    {
        return 'fa-file-text';
    }

    /**
     * @return mixed
     */
    public function allowsSubPages()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function showMeta()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canStaticCache()
    {
        return false;
    }
}