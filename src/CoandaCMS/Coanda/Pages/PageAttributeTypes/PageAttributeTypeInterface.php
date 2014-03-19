<?php namespace CoandaCMS\Coanda\Pages\PageAttributeTypes;

interface PageAttributeTypeInterface {

	public function store($attribute, $data);

	public function data($attribute);

}
