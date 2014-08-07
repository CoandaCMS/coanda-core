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
		// Coanda::checkAccess('layout', 'edit');

		// return View::make('coanda::admin.modules.layout.index', [ 'blocks' => $blocks, 'layouts' => $layouts ]);
	}
}