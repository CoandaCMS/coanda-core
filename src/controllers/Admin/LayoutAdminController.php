<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session;

use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Exceptions\PermissionDenied;

use CoandaCMS\Coanda\Layout\Exceptions\LayoutNotFound;
use CoandaCMS\Coanda\Layout\Exceptions\LayoutBlockTypeNotFound;
use CoandaCMS\Coanda\Layout\Exceptions\LayoutBlockNotFound;

use CoandaCMS\Coanda\Controllers\BaseController;

class LayoutAdminController extends BaseController {

    private $layoutBlockRepository;

    public function __construct(\CoandaCMS\Coanda\Layout\Repositories\LayoutBlockRepositoryInterface $layoutBlockRepository)
	{
		$this->layoutBlockRepository = $layoutBlockRepository;

		$this->beforeFilter('csrf', array('on' => 'post'));
	}

    public function getIndex()
	{
		// Coanda::checkAccess('layout', 'edit');
		$layouts = Coanda::module('layout')->layouts();

		return View::make('coanda::admin.modules.layout.index', [ 'layouts' => $layouts ]);
	}

	public function getView($layout_identifier)
	{
		try
		{
			$layout = Coanda::module('layout')->layoutByIdentifier($layout_identifier);

			$regions = $layout->regions();

			return View::make('coanda::admin.modules.layout.view', [ 'layout' => $layout, 'regions' => $regions ]);
		}
		catch (LayoutNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout'));
		}
	}

	public function getRegion($layout_identifier, $region_identifier)
	{
		try
		{
			$layout = Coanda::module('layout')->layoutByIdentifier($layout_identifier);
			$region = $layout->region($region_identifier);

			if (!$region)
			{
				return Redirect::to(Coanda::adminUrl('layout'));
			}

			$default_blocks = $this->layoutBlockRepository->defaultBlocksForRegion($layout_identifier, $region_identifier);

			return View::make('coanda::admin.modules.layout.region', [ 'layout' => $layout, 'region' => $region, 'default_blocks' => $default_blocks ]);
		}
		catch (LayoutNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout'));
		}
	}



	public function getBlocks()
	{
		$block_list = $this->layoutBlockRepository->getBlockList(10);

		return View::make('coanda::admin.modules.layout.blocks', [ 'block_list' => $block_list ]);
	}

	public function getBlockCreate($block_type_identifier)
	{
		try
		{
			$type = Coanda::module('layout')->blockTypeByIdentifier($block_type_identifier);
			$block = $this->layoutBlockRepository->createNewBlock($type);

			return Redirect::to(Coanda::adminUrl('layout/block-editversion/' . $block->id . '/1'));
		}
		catch (LayoutBlockTypeNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
	}

	public function getBlockEdit($block_id)
	{
		try
		{
			$new_version = $this->layoutBlockRepository->createNewVersion($block_id);

			return Redirect::to(Coanda::adminUrl('layout/block-editversion/' . $block_id . '/' . $new_version));
		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
	}

	public function getBlockEditversion($block_id, $version_number)
	{
		try
		{
			$version = $this->layoutBlockRepository->getBlockVersion($block_id, $version_number);
			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			return View::make('coanda::admin.modules.layout.editblock', [ 'version' => $version, 'invalid_fields' => $invalid_fields ]);
		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
	}

	public function postBlockEditversion($block_id, $version_number)
	{
		try
		{
			$version = $this->layoutBlockRepository->getBlockVersion($block_id, $version_number);

			if (Input::has('discard'))
			{
				$this->layoutBlockRepository->discardDraftBlock($version);

				return Redirect::to(Coanda::adminUrl('layout/blocks'));
			}

			$this->layoutBlockRepository->saveDraftBlockVersion($version, Input::all());

			// Everything went OK, so now we can determine what to do based on the button
			if (Input::has('save') && Input::get('save') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('layout/block-editversion/' . $block_id . '/' . $version_number))->with('saved', true);
			}

			if (Input::has('save_exit') && Input::get('save_exit') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('layout/block-view/' . $block_id));
			}

			if (Input::has('publish') && Input::get('publish') == 'true')
			{
				$this->layoutBlockRepository->publishBlockVersion($version);

				return Redirect::to(Coanda::adminUrl('layout/block-view/' . $block_id));
			}

		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
		catch (ValidationException $exception)
		{
			if (Input::has('save_exit') && Input::get('save_exit') == 'true')
			{
				return Redirect::to(Coanda::adminUrl('layout/block-view/' . $block_id));
			}

			return Redirect::to(Coanda::adminUrl('layout/block-editversion/' . $block_id . '/' . $version_number))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}

	public function getBlockView($block_id)
	{
		try
		{
			$block = $this->layoutBlockRepository->getBlockById($block_id);	

			return View::make('coanda::admin.modules.layout.viewblock', [ 'block' => $block ]);
		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
	}

	public function getBlockDelete($block_id)
	{
		try
		{
			$block = $this->layoutBlockRepository->getBlockById($block_id);	

			return View::make('coanda::admin.modules.layout.confirmdeleteblock', [ 'block' => $block ]);
		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
	}

	public function postBlockDelete($block_id)
	{
		try
		{
			$this->layoutBlockRepository->deleteBlock($block_id);

			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
	}

}