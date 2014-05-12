<?php namespace CoandaCMS\Coanda\Core\Attributes;

/**
 * Interface AttributeType
 * @package CoandaCMS\Coanda\Core\Attributes
 */
abstract class AttributeType {

    /**
     * @return mixed
     */
    abstract public function identifier();

    /**
     * @return mixed
     */
    abstract public function edit_template();

    /**
     * @return mixed
     */
    abstract public function view_template();

    /**
     * @param $data
     * @param $is_required
     * @param $name
     * @return mixed
     */
    abstract public function store($data, $is_required, $name);

    /**
     * @param $data
     * @return mixed
     */
    abstract public function data($data);


    public function handleAction($action, $data)
    {

    }
}
