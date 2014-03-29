<?php

abstract class BaseTest extends \Orchestra\Testbench\TestCase {

	public function setUp()
	{
	    parent::setUp();
	}

	protected function getPackageProviders()
	{
	    return array('CoandaCMS\Coanda\CoandaServiceProvider');
	}

	protected function getPackageAliases()
	{
		return array(
		    'Coanda' => 'CoandaCMS\Coanda\Facades\Coanda'
		);
	}

	public function tearDown()
	{
		\Mockery::close();
	}	
}