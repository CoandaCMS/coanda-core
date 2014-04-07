<?php namespace CoandaCMS\Coanda\Pages;

use Route, App, Config;

use CoandaCMS\Coanda\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PageAttributeTypeNotFound;

class PagesModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

	public $name = 'pages';

	private $page_types = [];
	private $page_attribute_types = [];

	public function boot($coanda)
	{
		// Add the permissions
		$views = [
			'create',
			'edit',
			'remove',
			'move'
		];

		$coanda->addModulePermissions('pages', 'Pages', $views);

		// Ad the router to handle slug views
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

		// load the page types
		$page_types = Config::get('coanda::coanda.page_types');

		foreach ($page_types as $page_type)
		{
			if (class_exists($page_type))
			{
				$type = new $page_type($this);

				// TODO: validate the definition to ensure all the specified page attribute types are available.

				$this->page_types[$type->identifier] = $type;				
			}
		}

		// Load the attributes
		$page_attribute_types = Config::get('coanda::coanda.page_attribute_types');

		foreach ($page_attribute_types as $page_attribute_type)
		{
			$attribute_type = new $page_attribute_type;

			$this->page_attribute_types[$attribute_type->identifier] = $attribute_type;
		}
	}

	/**
	 * Returns the available page types
	 * @return Array
	 */
	public function availablePageTypes()
	{
		return $this->page_types;
	}

	/**
	 * Gets a specific page type by identifier
	 * @param  string $type The identifier of the page type
	 * @return CoandaCMS\Coanda\Pages\PageTypeInterface
	 */
	public function getPageType($type)
	{
		if (array_key_exists($type, $this->page_types))
		{
			return $this->page_types[$type];
		}

		throw new PageTypeNotFound;
	}

	/**
	 * Get a specific attribute by identifier
	 * @param  string $type_identifier
	 * @return CoandaCMS\Coanda\Pages\PageAttributeTypeInterface
	 */
	public function getPageAttributeType($type_identifier)
	{
		if (array_key_exists($type_identifier, $this->page_attribute_types))
		{
			return $this->page_attribute_types[$type_identifier];
		}

		throw new PageAttributeTypeNotFound;
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