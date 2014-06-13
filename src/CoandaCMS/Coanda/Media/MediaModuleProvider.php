<?php namespace CoandaCMS\Coanda\Media;

use Route, App, Config;

use CoandaCMS\Coanda\Exceptions\PermissionDenied;

/**
 * Class MediaModuleProvider
 * @package CoandaCMS\Coanda\Media
 */
class MediaModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

    /**
     * @var string
     */
    public $name = 'media';

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
            'remove' => [
                'name' => 'Remove',
                'options' => []
            ],
            'tag' => [
                'name' => 'Tag',
                'options' => []
            ]
        ];

		$coanda->addModulePermissions('media', 'Media', $permissions);
	}

    /**
     *
     */
    public function adminRoutes()
	{
		// Load the media controller
		Route::controller('media', 'CoandaCMS\Coanda\Controllers\Admin\MediaAdminController');
	}

    /**
     *
     */
    public function userRoutes()
	{
	}

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return mixed
     */
    public function bindings(\Illuminate\Foundation\Application $app)
	{
		$app->bind('CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface', 'CoandaCMS\Coanda\Media\Repositories\Eloquent\EloquentMediaRepository');
	}

    /**
     * @param $permission
     * @param $parameters
     * @param $user_permissions
     * @return bool
     * @throws \CoandaCMS\Coanda\Exceptions\PermissionDenied
     */
    public function checkAccess($permission, $parameters, $user_permissions)
    {
        if (in_array('*', $user_permissions))
        {
            return true;
        }

        // If we anything in pages, we allow view
        if ($permission == 'view')
        {
            return;
        }

        // If we don't have this permission in the array, the throw right away
        if (!in_array($permission, $user_permissions))
        {
            throw new PermissionDenied('Access denied by media module: ' . $permission);
        }

        return;
    }

    /**
     * @param $coanda
     */
    public function buildAdminMenu($coanda)
    {
        if ($coanda->canViewModule('media'))
        {
            $coanda->addMenuItem('media', 'Media');
        }
    }

    /**
     * @param $file
     * @return mixed
     */
    public function handleUpload($file, $module_identifier = '')
    {
        $mediaRepository = App::make('CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface');

        return $mediaRepository->handleUpload($file, $module_identifier);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getMedia($id)
    {
        $mediaRepository = App::make('CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface');

        return $mediaRepository->findById($id);
    }

}