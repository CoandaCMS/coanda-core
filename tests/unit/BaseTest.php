<?php

class BaseTest extends \Orchestra\Testbench\TestCase {

	public function setUp()
	{
	    parent::setUp();
	}

	protected function getPackageProviders()
	{
	    return array('CoandaCMS\Coanda\CoandaServiceProvider');
	}

}