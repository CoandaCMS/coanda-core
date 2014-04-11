<?php namespace CoandaCMS\Coanda\Media;

use Route, App, Config;

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
		$views = [
			'create',
			'remove'
		];

		$coanda->addModulePermissions('media', 'Media', $views);
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
}