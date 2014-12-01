<?php namespace CoandaCMS\Coanda\Pages;

use CoandaCMS\Coanda\Urls\Slugifier;

abstract class PageType {

    /**
     * @var
     */
    protected $name;
    /**
     * @var
     */
    protected $identifier;
    /**
     * @var string
     */
    protected $icon = 'fa-file-text';
    /**
     * @var bool
     */
    protected $allow_sub_pages = true;
    /**
     * @var bool
     */
    protected $show_meta = true;
    /**
     * @var bool
     */
    protected $static_cache = true;
    /**
     * @var bool
     */
    protected $indexable = true;
    /**
     * @var
     */
    protected $schema = [
        'name' => 'Name|textline|required|generates_slug',
        'content' => 'Content|html'
    ];
    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var array
     */
    protected $templates = [];

    protected $allowed_sub_page_types = [];

    /**
     * @var
     */
    protected $layout;

    /**
     *
     */
    public function __construct()
    {
        $this->checkSetup();
        $this->buildAttributes();
    }

    /**
     *
     */
    private function checkSetup()
    {
        if (!$this->name)
        {
            throw new \InvalidArgumentException('Please specify a name for this page type: ' . get_class($this));
        }

        if (!$this->identifier)
        {
            $this->identifier = str_replace('-', '_', Slugifier::convert($this->name));
        }
    }

    /**
     *
     */
    private function buildAttributes()
    {
        if (is_array($this->schema))
        {
            $this->buildAttributesFromAttribute();
        }
    }

    /**
     *
     */
    private function buildAttributesFromAttribute()
    {
        foreach ($this->schema as $attribute_identifier => $attribute_schema)
        {
            $this->attributes[$attribute_identifier] = $this->processAttributeSchema($attribute_schema);
        }
    }

    /**
     * @param $attribute_schema
     * @return mixed
     */
    private function processAttributeSchema($attribute_schema)
    {
        $attribute_schema_parts = explode('|', $attribute_schema);

        if (count($attribute_schema_parts) < 2)
        {
            throw new \InvalidArgumentException('Please specify both a name and a type for this attribute (' . var_export($attribute_schema, true) . ')');
        }

        $attribute = [
            'name' => $attribute_schema_parts[0],
            'type' => $attribute_schema_parts[1],
        ];

        return $this->fetchAdditionalAttributeProperties(array_slice($attribute_schema_parts, 2), $attribute);
    }

    /**
     * @param $properties
     * @param $attribute
     * @return mixed
     */
    private function fetchAdditionalAttributeProperties($properties, $attribute)
    {
        foreach ($properties as $property)
        {
            if ($property == 'required')
            {
                $attribute['required'] = true;
            }

            if ($property == 'generates_slug')
            {
                $attribute['generates_slug'] = true;
            }   

            if (strpos($property, ':'))
            {
                $property_elements = explode(':', $property);

                $attribute[$property_elements[0]] = $this->processAttributeProperty($property_elements[1]);
            }
        }

        return $attribute;
    }

    /**
     * @param $property_elements
     * @return mixed
     */
    private function processAttributeProperty($property_elements)
    {
        if(strpos($property_elements, ','))
        {
            return $this->processArrayProperty($property_elements);
        } 
        else
        {
            return $this->processStringProperty($property_elements);
        }
    }

    /**
     * @param $property_elements
     * @return array
     */
    private function processArrayProperty($property_elements)
    {
        $elements = explode(',', $property_elements);
        $array = [];

        foreach ($elements as $element)
        {
            if (strpos($element, ';'))
            {
                $element_parts = explode(';', $element);

                $array[$element_parts[0]] = $element_parts[1];
            }
            else
            {
                $array[] = $element;
            }
        }

        return $array;
    }

    /**
     * @param $property_element
     * @return array
     */
    private function processStringProperty($property_element)
    {
        return $property_element;
    }

    /**
     * @return mixed
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public function identifier()
    {
        return $this->identifier;
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param $version
     * @return mixed
     */
    public function generateName($version)
    {
        $first_attribute = $version->attributes()->first();

        if ($first_attribute)
        {
            return $first_attribute->typeData();
        }

        return '';
    }

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
        return $this->templates;
    }

    /**
     * @return mixed
     */
    public function icon()
    {
        return $this->icon;
    }

    /**
     * @return mixed
     */
    public function allowsSubPages()
    {
        return $this->allow_sub_pages;
    }

    /**
     * @return mixed
     */
    public function showMeta()
    {
        return $this->show_meta;
    }

    /**
     * @return bool
     */
    public function canStaticCache()
    {
        return $this->static_cache;
    }

    /**
     * @return bool
     */
    public function isIndexable()
    {
        return $this->indexable;
    }

    /**
     * @return array
     */
    public function allowedSubPageTypes()
    {
        return $this->allowed_sub_page_types;
    }

    /**
     * @return bool
     */
    public function defaultLayout()
    {
        return $this->layout;
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (method_exists($this, $name))
        {
            return $this->$name();
        }

        return null;
    }
}