<?php namespace CoandaCMS\Coanda\Layout;

use Route, App, Config, Coanda, View;

use CoandaCMS\Coanda\Layout\Exceptions\LayoutNotFound;

/**
 * Class LayoutModuleProvider
 * @package CoandaCMS\Coanda\Layout
 */
class LayoutModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

    /**
     * @var string
     */
    public $name = 'layout';

    /**
     * @var array
     */
    private $layouts = [];

    private $block_types = [];

    /**
     * @var array
     */
    private $layouts_by_page_type = [];

    /**
     * @param CoandaCMS\Coanda\Coanda $coanda
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
		$this->loadLayouts();
        $this->loadBlockTypes();

        // Add the permissions
        $coanda->addModulePermissions('layout', 'Layouts', []); // No specific views for this module...
	}

    /**
     *
     */
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

    private function loadBlockTypes()
    {
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

    /**
     * @return array
     */
    public function layouts()
	{
		return $this->layouts;
	}

    /**
     * @param $identifier
     * @return mixed
     * @throws Exceptions\LayoutNotFound
     */
    public function layoutByIdentifier($identifier)
	{
		if (array_key_exists($identifier, $this->layouts))
		{
			return $this->layouts[$identifier];
		}

		throw new LayoutNotFound('Layout: ' . $identifier . ' not found.');
	}

    /**
     * @param $page_type
     * @return array
     */
    public function layoutsByPageType($page_type)
	{
		if (array_key_exists($page_type, $this->layouts_by_page_type))
		{
			return $this->layouts_by_page_type[$page_type];
		}

		return [];
	}

    public function layoutFor($for_identifier)
    {
        // Just returns the default for the moment.
        $default_layout = Config::get('coanda::coanda.default_layout');

        return $this->layouts[$default_layout];
    }

    public function defaultLayout()
    {
        return $this->layouts[Config::get('coanda::coanda.default_layout')];
    }

    /**
     * @return array
     */
    public function blockTypes()
    {
        return $this->block_types;
    }

    public function blockType($identifier)
    {
        if (isset($this->block_types[$identifier]))
        {
            return $this->block_types[$identifier];
        }

        return false;
    }

    /**
     *
     */
    public function adminRoutes()
	{
		// Load the layout controller
		Route::controller('layout', 'CoandaCMS\Coanda\Controllers\Admin\LayoutAdminController');
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
        $app->bind('CoandaCMS\Coanda\Layout\Repositories\LayoutRepositoryInterface', 'CoandaCMS\Coanda\Layout\Repositories\Eloquent\EloquentLayoutRepository');
	}

    /**
     * @param $permission
     * @param $parameters
     * @param array $user_permissions
     */
    public function checkAccess($permission, $parameters, $user_permissions = [])
	{
	}

    public function buildAdminMenu($coanda)
    {
        if ($coanda->canViewModule('layout'))
        {
            $coanda->addMenuItem('layout', 'Layouts');    
        }
    }

    public function getBlocks($layout_identifier, $region_identifier, $module_identifier, $cascade = true)
    {
        $layoutRepository = App::make('CoandaCMS\Coanda\Layout\Repositories\LayoutRepositoryInterface');

        return $layoutRepository->getBlocks($layout_identifier, $region_identifier, $module_identifier, $cascade);
    }

    public function getDefaultBlocks($layout_identifier, $region_identifier)
    {
        return $this->getBlocks($layout_identifier, $region_identifier, 'default', false);
    }
}