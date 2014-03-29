<?php

class General extends BaseTest {

	public function test_admin_url_returns_ok()
	{
		$this->call('GET', '/admin');
		$this->assertResponseStatus(200);
	}

}