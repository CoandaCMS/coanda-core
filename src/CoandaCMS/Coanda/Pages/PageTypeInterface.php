<?php namespace CoandaCMS\Coanda\Pages;

interface PageTypeInterface {
	
	// public $name;
	// public $identifier;

	public function attributes();

	public function generateName($version);

	public function showMeta();

}