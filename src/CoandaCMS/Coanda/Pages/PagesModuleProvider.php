<?php namespace CoandaCMS\Coanda\Pages;

use Route, App;

class PagesModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

	public function boot($coanda)
	{
		$views = [
			'create',
			'edit',
			'remove',
			'move'
		];

		$coanda->addModulePermissions('pages', 'Pages', $views);

		$coanda->addRouter('page', function ($url) {

			$pageRepository = App::make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');

			try
			{
				$page = $pageRepository->findById($url->urlable_id);	

				if ($page->is_trashed)
				{
					App::abort('404');
				}

				return 'View page: ' . $page->present()->name;
			}
			catch(\CoandaCMS\Coanda\Exceptions\PageNotFound $exception)
			{
				App::abort('404');
			}

		});
	}

	public function adminRoutes()
	{
		// Load the pages controller
		Route::controller('pages', 'CoandaCMS\Coanda\Controllers\Admin\PagesAdminController');
	}

	public function userRoutes()
	{
		// Front end routes for Pages (preview etc)
		Route::controller('pages', 'CoandaCMS\Coanda\Controllers\PagesController');
	}

	public function bindings($app)
	{
		$app->bind('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface', 'CoandaCMS\Coanda\Pages\Repositories\Eloquent\EloquentPageRepository');
	}

}