<?php namespace CoandaCMS\Coanda\Pages\PageAttributeTypes;

/**
 * Interface PageAttributeTypeInterface
 * @package CoandaCMS\Coanda\Pages\PageAttributeTypes
 */
interface PageAttributeTypeInterface {

	/**
	 * Perform the store
	 * @param  Attribute $attribute the attribute object
	 * @param  Array $data      All the data to be store
	 * @return void
	 */
	public function store($attribute, $data);

	/**
	 * Return the data for the attribute
	 * @param  Attribute $attribute the attribute object
	 * @return mixed
	 */
	public function data($attribute);

}
