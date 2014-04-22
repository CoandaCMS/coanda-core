<?php

abstract class BaseTest extends \PHPUnit_Framework_TestCase {

	public function setUp()
	{
	    parent::setUp();
	}

	public function tearDown()
	{
		\Mockery::close();
	}	
}