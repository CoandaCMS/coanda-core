<?php namespace CoandaCMS\Coanda\Pages;

use CoandaCMS\Coanda\CoandaModuleProvider;
use Route, App, Config, Coanda, View, Cache;

use CoandaCMS\Coanda\Pages\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Pages\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Pages\Exceptions\PageAttributeTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PermissionDenied;

/**
 * Class PagesModuleProvider
 * @package CoandaCMS\Coanda\Pages
 */
class PagesModuleProvider implements CoandaModuleProvider {

    /**
     * @var string
     */
    public $name = 'pages';

    /**
     * @var array
     */
    private $page_types = [];

    /**
     * @var array
     */
    private $home_page_types = [];

    /**
     * @var array
     */
    private $publish_handlers = [];

    /**
     * @param \CoandaCMS\Coanda\Coanda $coanda
     * @return mixed|void
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
		$this->loadRouter($coanda);
		$this->loadPageTypes($coanda);
		$this->loadPublishHandlers($coanda);
		$this->loadPermissions($coanda);
	}

    /**
     * @param $coanda
     */
    private function loadRouter($coanda)
	{
		// Add the router to handle slug views
		$coanda->addRouter('page', function ($url) use ($coanda) {

            $renderer = App::make('CoandaCMS\Coanda\Pages\Renderer\PageRenderer');
            return $renderer->renderPage($url->type_id);

		});
	}

    /**
     * @param $coanda
     */
    private function loadPageTypes($coanda)
	{
		// load the page types
		$page_types = Config::get('coanda::coanda.page_types');

		foreach ($page_types as $page_type)
		{
			if (class_exists($page_type))
			{
				$type = new $page_type($this);

				$this->page_types[$type->identifier()] = $type;
			}
		}

		// load the home page types
		$home_page_types = Config::get('coanda::coanda.home_page_types');

		foreach ($home_page_types as $home_page_type)
		{
			if (class_exists($home_page_type))
			{
				$type = new $home_page_type($this);

				$this->home_page_types[$type->identifier()] = $type;
			}
		}
	}

    /**
     * @param $coanda
     */
    private function loadPublishHandlers($coanda)
	{
		// Load the publish handlers
		$core_publish_handlers = [
			'CoandaCMS\Coanda\Pages\PublishHandlers\Immediate' // Make sure this one is always added (TODO: Consider removing this as 'core')
		];

		$enabled_publish_handlers = Config::get('coanda::coanda.publish_handlers');

		$publish_handlers = array_merge($core_publish_handlers, $enabled_publish_handlers);

		foreach ($publish_handlers as $publish_handler)
		{
			if (class_exists($publish_handler))
			{
				$handler = new $publish_handler;

				$this->publish_handlers[$handler->identifier] = $handler;
			}
		}
	}

    /**
     * @param $coanda
     */
    private function loadPermissions($coanda)
	{
		$publish_handler_options = [];

		foreach ($this->publish_handlers as $publish_handler)
		{
			$publish_handler_options[$publish_handler->identifier] = $publish_handler->name;
		}

		$page_type_options = [];

		foreach ($this->page_types as $page_type)
		{
			$page_type_options[$page_type->identifier()] = $page_type->name();
		}

		// Add the permissions
		$permissions = [
			'create' => [
				'name' => 'Create',
			],
			'edit' => [
				'name' => 'Edit',
			],
			'remove' => [
				'name' => 'Remove',
			],
			'publish_options' => [
				'name' => 'Publish options',
				'options' => $publish_handler_options
			],
			'page_types' => [
				'name' => 'Available page types',
				'options' => $page_type_options
			],
			'home_page' => [
				'name' => 'Home Page',
			],
			'locations' => [
				'name' => 'Locations',
				'location_paths' => true
			],
		];

		$coanda->addModulePermissions('pages', 'Pages', $permissions);		
	}

    /**
     * @param bool $page
     * @return array
     */
    public function availablePageTypes($page = false)
	{
		$page_types = $this->page_types;

		if ($page !== false)
		{
			$allowed_page_types = $page->pageType()->allowedSubPageTypes();

			if (count($allowed_page_types) > 0)
			{
				$page_types = [];

				foreach ($allowed_page_types as $allowed_page_type)
				{
					if (isset($this->page_types[$allowed_page_type]))
					{
						$page_types[$allowed_page_type] = $this->page_types[$allowed_page_type];
					}
				}
			}
		}

		$user_permissions = \Coanda::currentUserPermissions();

		if (isset($user_permissions['everything']) && in_array('*', $user_permissions['everything']))
		{
			return $page_types;
		}

		if (isset($user_permissions['pages']))
		{
			if (in_array('*', $user_permissions['pages']))
			{
				return $page_types;
			}

			if (in_array('create', $user_permissions['pages']))
			{
				if (isset($user_permissions['pages']['page_types']))
				{
					$new_page_types = [];

					foreach ($user_permissions['pages']['page_types'] as $permissioned_page_type)
					{
						if (isset($page_types[$permissioned_page_type]))
						{
							$new_page_types[$permissioned_page_type] = $page_types[$permissioned_page_type];
						}
					}

					return $new_page_types;
				}
				else
				{
					return $page_types;
				}
			}
		}

		return [];
	}

    /**
     * @return mixed
     */
    public function getPageRepository()
	{
		return App::make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');
	}


	/**
	 * @return mixed
	 */
	public function getPageManager()
	{
		return App::make('CoandaCMS\Coanda\Pages\PageManager');
	}

	/**
     * @param bool $page
     * @return array
     */
    public function availableHomePageTypes($page = false)
	{
		return $this->home_page_types;
	}

    /**
     * @param $type
     * @throws Exceptions\PageTypeNotFound
     * @return mixed
     */
    public function getPageType($type)
	{
		if (array_key_exists($type, $this->page_types))
		{
			return $this->page_types[$type];
		}

		throw new \CoandaCMS\Coanda\Pages\Exceptions\PageTypeNotFound;
	}

    /**
     * @param $type
     * @throws Exceptions\PageTypeNotFound
     * @return mixed
     */
    public function getHomePageType($type)
	{
		if (array_key_exists($type, $this->home_page_types))
		{
			return $this->home_page_types[$type];
		}

		throw new PageTypeNotFound;
	}

    /**
     * @return array
     */
    public function publishHandlers()
	{
		return $this->publish_handlers;
	}

    /**
     * @param $identifier
     * @return mixed
     */
    public function getPublishHandler($identifier)
	{
		return $this->publish_handlers[$identifier];
	}

    /**
     *
     */
    public function adminRoutes()
	{
		// Load the pages controller
		Route::controller('pages', 'CoandaCMS\Coanda\Controllers\Admin\PagesAdminController');
	}

    /**
     *
     */
    public function userRoutes()
	{
		// Front end routes for Pages (preview etc)
		Route::controller('pages', 'CoandaCMS\Coanda\Controllers\PagesController');
	}

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return mixed
     */
    public function bindings(\Illuminate\Foundation\Application $app)
	{
		$app->bind('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface', 'CoandaCMS\Coanda\Pages\Repositories\Eloquent\EloquentPageRepository');
	}

    /**
     * @param $permission
     * @param $parameters
     * @param array $user_permissions
     * @return mixed|void
     * @throws \CoandaCMS\Coanda\Exceptions\PermissionDenied
     */
    public function checkAccess($permission, $parameters, $user_permissions = [])
	{
		// Do we need to check the path permissions?
		if (isset($user_permissions['allowed_paths']) && count($user_permissions['allowed_paths']) > 0)
		{
			// Lets assume it passes
			$pass_path_check = true;

			if (isset($parameters['page_id']))
			{
				$page = Coanda::pages()->getPage($parameters['page_id']);

				if ($page && isset($user_permissions['allowed_paths']) && count($user_permissions['allowed_paths']) > 0)
				{
					$pass_path_check = false;

					$page_path = $page->path . ($page->path == '' ? '/' : '') . $page->id . '/';

					foreach ($user_permissions['allowed_paths'] as $allowed_path)
					{
						if ($allowed_path == '')
						{
							continue;
						}

						if (preg_match('/^' . preg_replace('/\//', '\/', preg_quote($allowed_path)) . '/', $page_path))
						{
							$pass_path_check = true;
						}

						if ($permission == 'view')
						{
							if (preg_match('/^' . preg_replace('/\//', '\/', preg_quote($page_path)) . '/', $allowed_path))
							{
								$pass_path_check = true;
							}
						}
					}
				}
			}

			if (!$pass_path_check)
			{
				throw new PermissionDenied('Your are not allowed access to this location');
			}
		}

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
			throw new PermissionDenied('Access denied by pages module: ' . $permission);
		}

		// Page type check
		if ($permission == 'edit' || $permission == 'remove')
		{
			if (isset($user_permissions['page_types']) && count($user_permissions['page_types']) > 0)
			{
				if (isset($parameters['page_type']) && !in_array($parameters['page_type'], $user_permissions['page_types']))
				{
                    throw new PermissionDenied('Access denied by pages module for page type: ' . $parameters['page_type']);
				}
			}
		}

		return;
	}

    /**
     * @param $coanda
     * @return mixed|void
     */
    public function buildAdminMenu($coanda)
	{
		if ($coanda->canViewModule('pages'))
		{
			$coanda->addMenuItem('pages', 'Pages');	
		}
	}

    /**
     * @return mixed
     * @throws \Exception
     */
    public function renderHome()
	{
        $renderer = App::make('CoandaCMS\Coanda\Pages\Renderer\PageRenderer');
        return $renderer->renderHomePage();
	}

    /**
     * @param $page_id
     * @return bool
     */
    public function getPage($page_id)
	{
		try
		{
			return $this->getPageRepository()->findById($page_id);
		}
		catch (PageNotFound $exception)
		{
			return false;
		}
	}

    /**
     * @param $slug
     * @return bool
     */
    public function bySlug($slug)
	{
		try
		{
			return $this->getPageRepository()->findBySlug($slug);
		}
		catch (PageNotFound $exception)
		{
			return false;
		}
	}

	public function byRemoteId($remote_id)
	{
		try
		{
			return $this->getPageManager()->getPageByRemoteId($remote_id);
		}
		catch (PageNotFound $exception)
		{
			return false;
		}
	}

    /**
     * @return mixed
     */
    private function getQueryBuilder()
	{
		return App::make('CoandaCMS\Coanda\Pages\PageQuery');
	}

    /**
     * @return mixed
     */
    public function query()
	{
		return $this->getQueryBuilder();
	}

    /**
     * @param $query
     * @return mixed
     */
    public function adminSearch($query)
	{
		return $this->getPageRepository()->adminSearch($query);
	}

    /**
     * @param $path
     * @return bool
     */
    public function byPath($path)
	{
		$path_parts = explode('/', trim($path, '/'));

		if (count($path_parts) > 0)
		{
			$page_id = (int) array_pop($path_parts);

			try
			{
				return $this->getPageManager()->getPage($page_id);
			}
			catch (PageNotFound $exception)
			{
				return false;
			}
		}

		return false;
	}
}