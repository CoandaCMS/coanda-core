<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

interface PublishHandlerInterface {
	
	// public $name;
	// public $identifier;

    public function display($data);

    public function validate($version, $data);

    public function execute($version, $data, $pageFactory, $urlRepository);
}