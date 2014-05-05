<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session;

use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Exceptions\PermissionDenied;

use CoandaCMS\Coanda\Layout\Exceptions\LayoutNotFound;
use CoandaCMS\Coanda\Layout\Exceptions\LayoutBlockTypeNotFound;
use CoandaCMS\Coanda\Layout\Exceptions\LayoutBlockNotFound;
use CoandaCMS\Coanda\Layout\Exceptions\LayoutBlockVersionNotFound;

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

	public function getViewRegion($layout_identifier, $region_identifier)
	{
		try
		{
			$layout = Coanda::module('layout')->layoutByIdentifier($layout_identifier);
			$region = $layout->region($region_identifier);

			if (!$region)
			{
				return Redirect::to(Coanda::adminUrl('layout'));
			}

			$region_blocks = $this->layoutBlockRepository->regionBlocks($layout->identifier(), $region->identifier());

			return View::make('coanda::admin.modules.layout.viewregion', [ 'layout' => $layout, 'region' => $region, 'region_blocks' => $region_blocks ]);
		}
		catch (LayoutNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout'));
		}
	}

	public function postViewRegion($layout_identifier, $region_identifier)
	{
		try
		{
			$layout = Coanda::module('layout')->layoutByIdentifier($layout_identifier);
			$region = $layout->region($region_identifier);

			if (!$region)
			{
				return Redirect::to(Coanda::adminUrl('layout'));
			}

			if (Input::has('update_order') && Input::get('update_order') == 'true')
			{
				$this->layoutBlockRepository->updateRegionOrdering($layout_identifier, $region_identifier, Input::get('ordering'));

				return Redirect::to(Coanda::adminUrl('layout/view-region/' . $layout_identifier . '/' . $region_identifier))->with('ordering_updated', true);
			}
		}
		catch (LayoutNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout'));
		}

	}



	public function getBlocks()
	{
		$block_list = $this->layoutBlockRepository->getBlockList(10);
		$layouts = Coanda::module('layout')->layouts();

		return View::make('coanda::admin.modules.layout.blocks', [ 'block_list' => $block_list, 'layouts' => $layouts ]);
	}

	public function getBlockCreate($block_type_identifier, $layout_identifier, $region_identifier)
	{
		try
		{
			$type = Coanda::module('layout')->blockTypeByIdentifier($block_type_identifier);
			$block = $this->layoutBlockRepository->createNewBlock($type, $layout_identifier, $region_identifier);

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
			$block = $this->layoutBlockRepository->getBlockById($block_id);
			$existing_drafts = $block->drafts();

			if ($existing_drafts->count() > 0)
			{
				return Redirect::to(Coanda::adminUrl('layout/block-existing-drafts/' . $block->id));
			}
			else
			{
				$new_version = $this->layoutBlockRepository->createNewVersion($block_id);

				return Redirect::to(Coanda::adminUrl('layout/block-editversion/' . $block_id . '/' . $new_version));
			}			
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
			$layouts = Coanda::module('layout')->layouts();

			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			return View::make('coanda::admin.modules.layout.editblock', [ 'version' => $version, 'invalid_fields' => $invalid_fields, 'layouts' => $layouts ]);
		}
		catch (LayoutBlockVersionNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));	
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
		catch (LayoutBlockVersionNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));	
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
			$available_regions = $block->availableRegions();

			return View::make('coanda::admin.modules.layout.viewblock', [ 'block' => $block, 'available_regions' => $available_regions ]);
		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
	}

	public function postBlockView($block_id)
	{
		try
		{
			if (Input::has('add_default') && Input::get('add_default') == 'true')
			{
				if (Input::has('add_region'))
				{
					$this->layoutBlockRepository->addDefaultBlockToRegion($block_id, Input::get('add_region'));

					return Redirect::to(Coanda::adminUrl('layout/block-view/' . $block_id))->with('region_added', true);
				}
			}

			return Redirect::to(Coanda::adminUrl('layout/block-view/' . $block_id));
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

	public function getBlockRemoveversion($block_id, $version_number)
	{
		try
		{
			$version = $this->layoutBlockRepository->getBlockVersion($block_id, $version_number);

			$this->layoutBlockRepository->discardDraftBlock($version);

			return Redirect::to(Coanda::adminUrl('layout/block-view/' . $block_id));
		}
		catch (LayoutBlockVersionNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));	
		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
	}

	public function getBlockExistingDrafts($block_id)
	{
		try
		{
			$block = $this->layoutBlockRepository->getBlockById($block_id);	

			return View::make('coanda::admin.modules.layout.existingblockdrafts', [ 'block' => $block ]);
		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}
	}

	public function postBlockExistingDrafts($block_id)
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

	public function getRemoveBlockFromRegion($block_id, $layout_identifier, $region_identifier)
	{
		try
		{
			$this->layoutBlockRepository->removeDefaultBlockFromRegion($block_id, $layout_identifier, $region_identifier);

			return Redirect::to(Coanda::adminUrl('layout/block-view/' . $block_id))->with('region_removed', true);
		}
		catch (LayoutBlockNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('layout/blocks'));
		}

	}
}