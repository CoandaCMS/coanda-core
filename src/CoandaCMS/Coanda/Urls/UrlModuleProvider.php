<?php namespace CoandaCMS\Coanda\Urls;

use Route, App, Config;

use CoandaCMS\Coanda\Exceptions\PermissionDenied;

class UrlModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

    /**
     * @var string
     */
    public $name = 'urls';

    /**
     * @param \CoandaCMS\Coanda\Coanda $coanda
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
		// Add the permissions
        $permissions = [
            'view' => [
                'name' => 'View',
                'options' => []
            ],
            'add' => [
                'name' => 'Add',
                'options' => []
            ],
            'remove' => [
                'name' => 'Remove',
                'options' => []
            ]
        ];

		$coanda->addModulePermissions('urls', 'Urls', $permissions);

        // Add the router to handle promo urls
        $coanda->addRouter('promourl', function ($url) use ($coanda) {

            $urlRepository = App::make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');
            $promo = $urlRepository->getPromoUrl($url->type_id);

            if ($promo)
            {
                $promo->addHit();

                return \Redirect::to(url($promo->destination));
            }

            App::abort('404');

        });
	}

    /**
     *
     */
    public function adminRoutes()
	{
		// Load the media controller
		Route::controller('urls', 'CoandaCMS\Coanda\Controllers\Admin\UrlAdminController');
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
	}

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

    public function buildAdminMenu($coanda)
    {
        if ($coanda->canViewModule('urls'))
        {
            $coanda->addMenuItem('urls', 'Urls');
        }
    }
}