<?php namespace CoandaCMS\Coanda\Core\Attributes;

interface AttributeTypeInterface {

	public function identifier();

	public function store($data, $is_required, $name);

	public function data($data);

}
