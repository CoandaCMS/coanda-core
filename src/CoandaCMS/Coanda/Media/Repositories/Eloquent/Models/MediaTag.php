<?php namespace CoandaCMS\Coanda\Media\Repositories\Eloquent\Models;

use Eloquent, Coanda, App, Config, File;
use Carbon\Carbon;

class MediaTag extends Eloquent {

	protected $table = 'mediatags';

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Media\Presenters\MediaTag';

	public function media()
	{
		return $this->belongsToMany('CoandaCMS\Coanda\Media\Repositories\Eloquent\Models\Media');
	}
}