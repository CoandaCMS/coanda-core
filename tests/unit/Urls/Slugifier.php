<?php

class Slugifier extends BaseTest {

	public function test_valid_slug()
	{
		$slugifier = new \CoandaCMS\Coanda\Urls\Slugifier;

		$this->assertTrue($slugifier->validate('this-is-valid'));
	}

	public function test_invalid_slug()
	{
		$slugifier = new \CoandaCMS\Coanda\Urls\Slugifier;

		$this->assertFalse($slugifier->validate('this is not valid'));
	}
	
	public function test_convert_simple()
	{
		$slugifier = new \CoandaCMS\Coanda\Urls\Slugifier;

		$this->assertEquals('simple-string', $slugifier->convert('simple string'));
	}

	public function test_convert_complex()
	{
		$slugifier = new \CoandaCMS\Coanda\Urls\Slugifier;

		$this->assertEquals('remplace-les-caracteres-speciaux', $slugifier->convert("Remplace les caractères spéciaux"));
	}	
}