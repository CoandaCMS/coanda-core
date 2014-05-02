<?php namespace CoandaCMS\Coanda\Core\Attributes;

interface AttributeTypeInterface {

	public function identifier();

    public function edit_template();

    public function view_template();

	public function store($data, $is_required, $name);

	public function data($data);

}
