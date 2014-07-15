<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Input, Redirect, Session;

use CoandaCMS\Coanda\Controllers\BaseController;

use CoandaCMS\Coanda\Exceptions\ValidationException;

/**
 * Class LayoutAdminController
 * @package CoandaCMS\Coanda\Controllers\Admin
 */
class LayoutAdminController extends BaseController {

    private $layoutRepository;

    /**
     */
    public function __construct(\CoandaCMS\Coanda\Layout\Repositories\LayoutRepositoryInterface $layoutRepository)
	{
		$this->layoutRepository = $layoutRepository;

		$this->beforeFilter('csrf', array('on' => 'post'));
	}

    /**
     * @return mixed
     */
    public function getIndex()
	{
		Coanda::checkAccess('layout', 'edit');

		$blocks = $this->layoutRepository->getPaginatedBlocks(10);
		$layouts = Coanda::layout()->layouts();

		return View::make('coanda::admin.modules.layout.index', [ 'blocks' => $blocks, 'layouts' => $layouts ]);
	}

	public function getAddBlock($block_type_identifier)
	{
		Coanda::checkAccess('layout', 'edit');

		$block_type = Coanda::layout()->blockType($block_type_identifier);

		if (!$block_type)
		{
			App::abort('404');
		}

		$old_attribute_input = Input::old('attributes', []);
		$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

		return View::make('coanda::admin.modules.layout.addblock', [ 'block_type' => $block_type, 'old_attribute_input' => $old_attribute_input, 'invalid_fields' => $invalid_fields ]);
	}

	public function postAddBlock($block_type_identifier)
	{
		if (Input::has('cancel') && Input::get('cancel') == 'true')
		{
			return Redirect::to(Coanda::adminUrl('layout'));
		}

		$block_type = Coanda::layout()->blockType($block_type_identifier);

		if (!$block_type)
		{
			App::abort('404');
		}

		try
		{
			$block = $this->layoutRepository->addBlock($block_type, Input::all());

			return Redirect::to(Coanda::adminUrl('layout/block/' . $block->id));
		}
		catch (ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/add-block/' . $block_type_identifier))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}

	public function getEditBlock($block_id)
	{
		$block = $this->layoutRepository->getBlock($block_id);

		if (!$block)
		{
			App::abort('404');
		}

		$old_attribute_input = Input::old('attributes', []);
		$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

		return View::make('coanda::admin.modules.layout.editblock', [ 'block' => $block, 'old_attribute_input' => $old_attribute_input, 'invalid_fields' => $invalid_fields ]);
	}

	public function postEditBlock($block_id)
	{
		$block = $this->layoutRepository->getBlock($block_id);

		if (!$block)
		{
			App::abort('404');
		}

		try
		{
			$this->layoutRepository->updateBlock($block, Input::all());

			return Redirect::to(Coanda::adminUrl('layout/block/' . $block->id));
		}
		catch (ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/edit-block/' . $block->id))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}

	public function getBlock($block_id)
	{
		$block = $this->layoutRepository->getBlock($block_id);

		if (!$block)
		{
			App::abort('404');
		}

		$region_assigments = $block->regionAssignmentsPaginated(10);

		return View::make('coanda::admin.modules.layout.block', [ 'block' => $block, 'region_assigments' => $region_assigments ]);
	}

	public function postBlock($block_id)
	{
		$block = $this->layoutRepository->getBlock($block_id);

		if (!$block)
		{
			App::abort('404');
		}

		$layout_region_identifier_parts = explode(':', Input::get('layout_region_identifier'));

		$this->layoutRepository->addRegionAssignment(
									$block_id,
									$layout_region_identifier_parts[0],
									$layout_region_identifier_parts[1],
									Input::get('module_identifier'),
									Input::has('cascade')
								);

		return Redirect::to(Coanda::adminUrl('layout/block/' . $block->id))->with('added', true);
	}

	public function getRemoveAssignment($assignment_id)
	{
		$assignment_block_id = $this->layoutRepository->removeAssignmentBlock($assignment_id);

		return Redirect::to(Coanda::adminUrl('layout/block/' . $assignment_block_id))->with('assignment_removed', true);
	}

	public function getView($layout_identifier)
	{
		$layout = Coanda::layout()->layoutByIdentifier($layout_identifier);

		if (!$layout)
		{
			App::abort('404');
		}

		return View::make('coanda::admin.modules.layout.view', [ 'layout' => $layout ]);
	}

	public function getRegion($layout_identifier, $region_identifier)
	{
		$layout = Coanda::layout()->layoutByIdentifier($layout_identifier);

		if (!$layout)
		{
			App::abort('404');
		}

		$region_name = isset($layout->regions()[$region_identifier]) ? $layout->regions()[$region_identifier]['name'] : false;

		if (!$region_name)
		{
			App::abort('404');
		}

		$module_identifiers = $this->layoutRepository->getModuleIdentifiersForRegion($layout_identifier, $region_identifier);

		return View::make('coanda::admin.modules.layout.region', [ 'layout' => $layout, 'region_identifier' => $region_identifier, 'region_name' => $region_name, 'module_identifiers' => $module_identifiers ]);
	}

	public function getModule($layout_identifier, $region_identifier, $module_identifier)
	{
		$layout = Coanda::layout()->layoutByIdentifier($layout_identifier);

		if (!$layout)
		{
			App::abort('404');
		}

		$region_name = isset($layout->regions()[$region_identifier]) ? $layout->regions()[$region_identifier]['name'] : false;

		if (!$region_name)
		{
			App::abort('404');
		}

		$assignments = $this->layoutRepository->getAssignmentsByModuleIdentifier($layout_identifier, $region_identifier, $module_identifier, 10);

		return View::make('coanda::admin.modules.layout.module', [ 'layout' => $layout, 'region_identifier' => $region_identifier, 'region_name' => $region_name, 'module_identifier' => $module_identifier, 'assignments' => $assignments ]);
	}

	public function postModule($layout_identifier, $region_identifier, $module_identifier)
	{
		if (Input::has('update_order'))
		{
			$this->layoutRepository->updateAssignmentOrders($layout_identifier, $region_identifier, $module_identifier, Input::get('ordering'));
		}

		return Redirect::to(Coanda::adminUrl('layout/module/' . $layout_identifier . '/' . $region_identifier . '/' . $module_identifier))->with('ordering_updated', true);
	}
}