<?php

abstract class BaseTest extends \PHPUnit_Framework_TestCase {

	public function setUp()
	{
	 //    parent::setUp();

	 //    $db_config = [
		// 	'driver'   => 'sqlite',
		// 	'database' => ':memory:',
		// 	'prefix'   => '',
		// ];

	 //    DB::addConnection($db_config);
	}

	public function tearDown()
	{
		\Mockery::close();
	}	
}