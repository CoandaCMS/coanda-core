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
     * @param $version
     * @param array $data
     * @return string
     */
    public function template($version, $data = [])
    {
        $template_name = $this->identifier();

        if ($version->template_identifier && $version->template_identifier !== 'default')
        {
            $template_name .= '_' . $version->template_identifier;
        }

        return 'pagetypes.' . $template_name;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function preRender($data)
    {
        // The default is just to return the data untouched...
        return $data;
    }

    /**
     * @return array
     */
    public function availableTemplates()
    {
        return [];
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
    public function allowsMultipleLocations()
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

    /**
     * @return array
     */
    public function allowedSubPageTypes()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function defaultLayout()
    {
        return false;
    }
}