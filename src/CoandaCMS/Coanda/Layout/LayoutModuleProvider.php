<?php namespace CoandaCMS\Coanda\Layout;

use Route, App, Config, Coanda, View;

use CoandaCMS\Coanda\Layout\Exceptions\LayoutNotFound;
use CoandaCMS\Coanda\Layout\Exceptions\LayoutBlockTypeNotFound;

class LayoutModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

    public $name = 'layout';

    private $layouts = [];

    private $layouts_by_page_type = [];

    private $block_types = [];

    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
		$this->loadLayouts();
		$this->loadBlockTypes($coanda);
	}

	private function loadBlockTypes($coanda)
	{
		// load the block types
		$block_types = Config::get('coanda::coanda.layout_block_types');

		foreach ($block_types as $block_type)
		{
			if (class_exists($block_type))
			{
				$type = new $block_type($this);

				$this->block_types[$type->identifier()] = $type;
			}
		}
	}

	public function availableBlockTypes()
	{
		return $this->block_types;
	}

	public function blockTypeByIdentifier($identifier)
	{
		if (array_key_exists($identifier, $this->block_types))
		{
			return $this->block_types[$identifier];
		}

		throw new LayoutBlockTypeNotFound('Block type: ' . $identifier . ' not found.');
	}

	private function loadLayouts()
	{
		$layouts = Config::get('coanda::coanda.layouts');

		foreach ($layouts as $layout_class)
		{
			if (class_exists($layout_class))
			{
				$layout = new $layout_class;

				$this->layouts[$layout->identifier()] = $layout;

				foreach ($layout->pageTypes() as $page_type)
				{
					$this->layouts_by_page_type[$page_type][] = $layout;
				}
			}
		}
	}

	public function layouts()
	{
		return $this->layouts;
	}

	public function layoutByIdentifier($identifier)
	{
		if (array_key_exists($identifier, $this->layouts))
		{
			return $this->layouts[$identifier];
		}

		throw new LayoutNotFound('Layout: ' . $identifier . ' not found.');
	}

	public function layoutsByPageType($page_type)
	{
		if (array_key_exists($page_type, $this->layouts_by_page_type))
		{
			return $this->layouts_by_page_type[$page_type];
		}

		return [];
	}

    public function adminRoutes()
	{
		// Load the layout controller
		Route::controller('layout', 'CoandaCMS\Coanda\Controllers\Admin\LayoutAdminController');
	}

	public function userRoutes()
	{
	}

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return mixed
     */
    public function bindings(\Illuminate\Foundation\Application $app)
	{
		$app->bind('CoandaCMS\Coanda\Layout\Repositories\LayoutBlockRepositoryInterface', 'CoandaCMS\Coanda\Layout\Repositories\Eloquent\EloquentLayoutBlockRepository');
	}

	public function checkAccess($permission, $parameters, $user_permissions = [])
	{
	}

}