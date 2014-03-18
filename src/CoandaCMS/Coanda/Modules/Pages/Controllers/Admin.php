<?php namespace CoandaCMS\Coanda\Modules\Pages\Controllers;

use View;

class Admin extends \CoandaCMS\Coanda\Controllers\Base {

	public function getIndex()
	{
		return View::make('coandapages::index');
	}

}