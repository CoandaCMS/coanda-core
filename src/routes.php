<?php

/*
|--------------------------------------------------------------------------
| The admin site
| URL can be configured in config/app.php (default is 'admin')
|--------------------------------------------------------------------------
*/
Route::group(array('prefix' => Config::get('coanda::coanda.admin_path')), function()
{
	Route::controller('/', 'CoandaCMS\Coanda\Controllers\Admin');

});

Route::get('/', function () {
	
	echo 'hello!';

});

