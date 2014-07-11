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

		return View::make('coanda::admin.modules.layout.index', [ 'blocks' => $blocks ]);
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
		$block_type = Coanda::layout()->blockType($block_type_identifier);

		if (!$block_type)
		{
			App::abort('404');
		}

		try
		{
			$block = $this->layoutRepository->addBlock($block_type, Input::all());	

			dd($block);
		}
		catch (ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/add-block/' . $block_type_identifier))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
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

		$block->addRegionAssignment($layout_region_identifier_parts[0], $layout_region_identifier_parts[1], Input::get('module_identifier'));

		return Redirect::to(Coanda::adminUrl('layout/block/' . $block->id))->with('added', true);
	}
}