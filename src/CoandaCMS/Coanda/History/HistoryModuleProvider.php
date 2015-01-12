<?php namespace CoandaCMS\Coanda\History;

use Route;
use CoandaCMS\Coanda\CoandaModuleProvider;

class HistoryModuleProvider implements CoandaModuleProvider {

    /**
     * @var string
     */
    public $name = 'history';

    /**
     * @param \CoandaCMS\Coanda\Coanda $coanda
     * @return mixed|void
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
    {
        // Add the permissions
        $permissions = [
            'view' => [
                'name' => 'View',
                'options' => []
            ],
        ];

        $coanda->addModulePermissions('history', 'History', $permissions);
    }

    /**
     *
     */
    public function adminRoutes()
    {
        Route::controller('history', 'CoandaCMS\Coanda\Controllers\Admin\HistoryAdminController');
    }

    /**
     *
     */
    public function userRoutes()
    {
    }

    public function checkAccess($permission, $parameters, $user_permissions)
    {
        if (in_array('*', $user_permissions))
        {
            return true;
        }

        // If we don't have this permission in the array, the throw right away
        if (!in_array($permission, $user_permissions))
        {
            throw new PermissionDenied('Access denied by history module: ' . $permission);
        }

        return;
    }

    public function bindings(\Illuminate\Foundation\Application $app)
    {
        $app->bind('CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface', 'CoandaCMS\Coanda\History\Repositories\Eloquent\EloquentHistoryRepository');
    }

    /**
     * @param $coanda
     * @return mixed|void
     */
    public function buildAdminMenu($coanda)
    {
        if ($coanda->canViewModule('history'))
        {
            $coanda->addMenuItem('history', 'History');
        }
    }
}