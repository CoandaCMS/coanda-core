<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda;

use CoandaCMS\Coanda\Controllers\BaseController;

/**
 * Class LayoutAdminController
 * @package CoandaCMS\Coanda\Controllers\Admin
 */
class LayoutAdminController extends BaseController {

    /**
     */
    public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

    /**
     * @return mixed
     */
    public function getIndex()
	{
		// Coanda::checkAccess('layout', 'edit');
		$layouts = Coanda::module('layout')->layouts();

		return View::make('coanda::admin.modules.layout.index', [ 'layouts' => $layouts ]);
	}
}