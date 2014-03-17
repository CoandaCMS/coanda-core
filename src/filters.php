<?php

/*
|--------------------------------------------------------------------------
| Admin authentication check
|--------------------------------------------------------------------------
*/
Route::filter('admin_auth', function()
{
    if (!Coanda::isLoggedIn())
    {
    	return Redirect::to('/' . Config::get('coanda::coanda.admin_path') . '/login');
    }

});
