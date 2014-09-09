<?php namespace CoandaCMS\Coanda\Media;

use CoandaCMS\Coanda\CoandaModuleProvider;
use Illuminate\Foundation\Application;
use Route, App, Config, Coanda, Redirect;

use CoandaCMS\Coanda\Exceptions\PermissionDenied;
use CoandaCMS\Coanda\Media\Exceptions\ImageGenerationException;
use CoandaCMS\Coanda\Media\Exceptions\OriginalFileCacheException;

/**
 * Class MediaModuleProvider
 * @package CoandaCMS\Coanda\Media
 */
class MediaModuleProvider implements CoandaModuleProvider {

    /**
     * @var string
     */
    public $name = 'media';

    /**
     * @param \CoandaCMS\Coanda\Coanda $coanda
     * @return mixed|void
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
		Route::controller('media', 'CoandaCMS\Coanda\Controllers\Admin\MediaAdminController');
	}

    /**
     *
     */
    public function userRoutes()
	{
        $image_cache_directory = Config::get('coanda::coanda.image_cache_directory');

        // Add the image caching route
        Route::get($image_cache_directory . '/{media_id}/{filename}', function ($media_id, $filename) use ($image_cache_directory) {

            $media = Coanda::media()->getById($media_id);

            // We can only do this for images...
            if ($media && $media->type == 'image')
            {
                try
                {
                    $media->generateImage($filename);

                    $redirect_url = $image_cache_directory . '/' . $media->id . '/' . $filename;

                    return Redirect::to(url($redirect_url));
                }
                catch (ImageGenerationException $exception)
                {
                    App::abort('404');
                }
            }

            App::abort('404');

        });

        $file_cache_directory = Config::get('coanda::coanda.file_cache_directory');

        // Add the file caching route
        Route::get($file_cache_directory . '/{media_id}/{filename}', function ($media_id, $filename) use ($file_cache_directory) {

            $media = Coanda::media()->getById($media_id);

            if ($media)
            {
                try
                {
                    $media->generateOriginalFileCache($filename);

                    $redirect_url = $file_cache_directory . '/' . $media->id . '/' . $filename;

                    return Redirect::to(url($redirect_url));
                }
                catch (OriginalFileCacheException $exception)
                {
                    App::abort('404');
                }
            }

            App::abort('404');

        });

	}

    /**
     * @param Application $app
     * @return mixed
     */
    public function bindings(Application $app)
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
     * @return mixed|void
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
     * @param string $module_identifier
     * @param bool $admin_only
     * @return mixed
     */
    public function handleUpload($file, $module_identifier = '', $admin_only = false)
    {
        $mediaRepository = App::make('CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface');

        return $mediaRepository->handleUpload($file, $module_identifier, $admin_only);
    }

    /**
     * @param $url
     * @param string $module_identifier
     * @param bool $default_extension
     * @return mixed
     */
    public function fromURL($url, $module_identifier = '', $default_extension = false)
    {
        $mediaRepository = App::make('CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface');

        return $mediaRepository->fromURL($url, $module_identifier, $default_extension);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getMedia($id)
    {
        return $this->getById($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        $mediaRepository = App::make('CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface');

        return $mediaRepository->findById($id);
    }
}