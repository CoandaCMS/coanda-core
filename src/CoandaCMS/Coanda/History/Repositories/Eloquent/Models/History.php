<?php namespace CoandaCMS\Coanda\History\Repositories\Eloquent\Models;

use Eloquent, Coanda, App;

class History extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

	protected $presenter = 'CoandaCMS\Coanda\History\Presenters\History';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'history';
}