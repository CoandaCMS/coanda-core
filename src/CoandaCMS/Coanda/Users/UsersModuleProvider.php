<?php namespace CoandaCMS\Coanda\Users;

use Route, App, Config;

class UsersModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

	public $name = 'users';

	public function boot($coanda)
	{
		// Add the permissions
		$views = [
			'create',
			'edit',
			'remove',
		];

		$coanda->addModulePermissions('users', 'Users', $views);
	}

	public function adminRoutes()
	{
		// Load the users controller
		Route::controller('users', 'CoandaCMS\Coanda\Controllers\Admin\UsersAdminController');
	}

	public function userRoutes()
	{
	}

	public function bindings($app)
	{
		$app->bind('CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface', 'CoandaCMS\Coanda\Users\Repositories\Eloquent\EloquentUserRepository');
	}
}