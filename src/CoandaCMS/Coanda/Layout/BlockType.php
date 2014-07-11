<?php namespace CoandaCMS\Coanda\Layout;

use Coanda;

abstract class BlockType {

    private $attributes;

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
    abstract public function blueprint();

    /**
     * @return string
     */
    public function template($version, $data = [])
    {
        $template_name = $this->identifier();

        return 'layoutblocktype.' . $template_name;
    }

    public function preRender($data)
    {
        // The default is just to return the data untouched...
        return $data;
    }

    public function attributes()
    {
        if (!$this->attributes)
        {
            foreach ($this->blueprint() as $attribute_identifier => $attribute_definition)
            {
                $this->attributes[$attribute_identifier] = new \stdClass;

                $this->attributes[$attribute_identifier]->identifier = $attribute_identifier;
                $this->attributes[$attribute_identifier]->name = $attribute_definition['name'];
                $this->attributes[$attribute_identifier]->required = $attribute_definition['required'];
                $this->attributes[$attribute_identifier]->type = Coanda::getAttributeType($attribute_definition['type']);
            }
        }

        return $this->attributes;
    }
}