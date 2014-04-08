<?php namespace CoandaCMS\Coanda\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Coanda
 * @package CoandaCMS\Coanda\Facades
 */
class Coanda extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'coanda'; }

}
