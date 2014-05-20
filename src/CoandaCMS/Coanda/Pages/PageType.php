<?php namespace CoandaCMS\Coanda\Pages;

/**
 * Interface PageTypeInterface
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

    public function canStaticCache()
    {
        return false;
    }
}