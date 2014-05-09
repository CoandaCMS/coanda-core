<?php namespace CoandaCMS\Coanda\Core\Attributes;

/**
 * Interface AttributeTypeInterface
 * @package CoandaCMS\Coanda\Core\Attributes
 */
interface AttributeTypeInterface {

    /**
     * @return mixed
     */
    public function identifier();

    /**
     * @return mixed
     */
    public function edit_template();

    /**
     * @return mixed
     */
    public function view_template();

    /**
     * @param $data
     * @param $is_required
     * @param $name
     * @return mixed
     */
    public function store($data, $is_required, $name);

    /**
     * @param $data
     * @return mixed
     */
    public function data($data);

}
