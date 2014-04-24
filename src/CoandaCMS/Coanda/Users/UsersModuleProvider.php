<?php namespace CoandaCMS\Coanda\Users;

use Route, App, Config;

use CoandaCMS\Coanda\Exceptions\PermissionDenied;

/**
 * Class UsersModuleProvider
 * @package CoandaCMS\Coanda\Users
 */
class UsersModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

    /**
     * @var string
     */
    public $name = 'users';

    /**
     * @param \CoandaCMS\Coanda\Coanda $coanda
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
        // Add the permissions
        $permissions = [
            'create' => [
                'name' => 'Create',
                'options' => []
            ],
            'edit' => [
                'name' => 'Edit',
                'options' => []
            ],
            'remove' => [
                'name' => 'Remove',
                'options' => []
            ]
        ];

		$coanda->addModulePermissions('users', 'Users', $permissions);
	}

    /**
     *
     */
    public function adminRoutes()
	{
		// Load the users controller
		Route::controller('users', 'CoandaCMS\Coanda\Controllers\Admin\UsersAdminController');
	}

    /**
     *
     */
    public function userRoutes()
	{
	}

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function bindings(\Illuminate\Foundation\Application $app)
	{
		$app->bind('CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface', 'CoandaCMS\Coanda\Users\Repositories\Eloquent\EloquentUserRepository');
	}

    public function checkAccess($permission, $parameters, $user_permissions)
    {
        if (in_array('*', $user_permissions))
        {
            return;
        }

        // If we anything in pages, we allow view
        if ($permission == 'view')
        {
            return;
        }

        // If we have create, but not edit, then add edit
        if (in_array('create', $user_permissions) && !in_array('edit', $user_permissions))
        {
            $user_permissions[] = 'edit';
        }

        // If we don't have this permission in the array, the throw right away
        if (!in_array($permission, $user_permissions))
        {
            throw new PermissionDenied('Access denied by users module: ' . $permission);
        }
    }

}