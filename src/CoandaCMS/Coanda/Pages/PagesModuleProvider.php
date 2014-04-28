<?php namespace CoandaCMS\Coanda\Pages;

use Route, App, Config;

use CoandaCMS\Coanda\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PageAttributeTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PermissionDenied;

/**
 * Class PagesModuleProvider
 * @package CoandaCMS\Coanda\Pages
 */
class PagesModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

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
    private $page_attribute_types = [];

    /**
     * @var array
     */
    private $publish_handlers = [];

    /**
     * @param \CoandaCMS\Coanda\Coanda $coanda
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
		$this->loadRouter($coanda);
		$this->loadPageTypes($coanda);
		$this->loadAttributes($coanda);
		$this->loadPublishHandlers($coanda);
		$this->loadPermissions($coanda);
	}

	private function loadRouter($coanda)
	{
		// Add the router to handle slug views
		$coanda->addRouter('page', function ($url) use ($coanda) {

			$pageRepository = App::make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');

			try
			{
				$page = $pageRepository->findById($url->urlable_id);	

				if ($page->is_trashed)
				{
					App::abort('404');
				}

				if (!$page->is_visible)
				{
					App::abort('404');
				}

				return $this->renderPage($page, $coanda->theme());
			}
			catch(\CoandaCMS\Coanda\Exceptions\PageNotFound $exception)
			{
				App::abort('404');
			}

		});
	}

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
	}

	private function loadAttributes($coanda)
	{
		// Load the attributes
		$page_attribute_types = Config::get('coanda::coanda.page_attribute_types');

		foreach ($page_attribute_types as $page_attribute_type)
		{
			$attribute_type = new $page_attribute_type;

			$this->page_attribute_types[$attribute_type->identifier] = $attribute_type;
		}
	}

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
				'options' => []
			],
			'edit' => [
				'name' => 'Edit',
				'options' => []
			],
			'remove' => [
				'name' => 'Remove',
				'options' => []
			],
			'publish_options' => [
				'name' => 'Publish options',
				'options' => $publish_handler_options
			],
			'page_types' => [
				'name' => 'Available page types',
				'options' => $page_type_options
			]
		];

		$coanda->addModulePermissions('pages', 'Pages', $permissions);		
	}

	/**
	 * Returns the available page types
	 * @return Array
	 */
	public function availablePageTypes($page = false)
	{
		$user_permissions = \Coanda::currentUserPermissions();

		if (isset($user_permissions['everything']) && in_array('*', $user_permissions['everything']))
		{
			return $this->page_types;	
		}

		if (isset($user_permissions['pages']))
		{
			if (in_array('*', $user_permissions['pages']))
			{
				return $this->page_types;
			}

			if (in_array('create', $user_permissions['pages']))
			{
				if (isset($user_permissions['pages']['page_types']))
				{
					$page_types = [];

					foreach ($user_permissions['pages']['page_types'] as $permissioned_page_type)
					{
						if (isset($this->page_types[$permissioned_page_type]))
						{
							$page_types[$permissioned_page_type] = $this->page_types[$permissioned_page_type];
						}
					}

					return $page_types;
				}
				else
				{
					return $this->page_types;
				}
			}
		}

		return [];
	}

    /**
     * @param $type
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageTypeNotFound
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
     * @param $type_identifier
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageAttributeTypeNotFound
     */
    public function getPageAttributeType($type_identifier)
	{
		if (array_key_exists($type_identifier, $this->page_attribute_types))
		{
			return $this->page_attribute_types[$type_identifier];
		}

		throw new PageAttributeTypeNotFound;
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

	public function checkAccess($permission, $parameters, $user_permissions = [])
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
			throw new PermissionDenied('Access denied by pages module: ' . $permission);
		}

		// Page type check
		if ($permission == 'create' || $permission == 'edit')
		{
			if (isset($user_permissions['page_types']) && count($user_permissions['page_types']) > 0)
			{
				if (!in_array($parameters['page_type'], $user_permissions['page_types']))
				{
					throw new PermissionDenied('Access denied by pages module for page type: ' . $parameters['page_type']);
				}
			}
		}
	}

	private function buildAttributes($page)
	{
		$attributes = [];

		foreach ($page->attributes as $attribute)
		{
			$attributes[$attribute->identifier] = [
				'identifier' => $attribute->identifier,
				'type' => $attribute->type,
				'order' => $attribute->order,
				'content' => $attribute->typeData(),
				'attribute_data' => $attribute->attribute_data,
			];
		}

		return $attributes;
	}

	private function buildMeta($page)
	{
		return [
			'title' => 'XXXXX',
			'description' => 'XXXXX'
		];
	}

	private function renderPage($page, $theme)
	{
		$render_data = [
			'name' => $page->present()->name,
			'type' => $page->type,
			'meta' => $this->buildMeta($page),
			'attributes' => $this->buildAttributes($page)
		];

		return $theme->render('page', $render_data);
	}
}