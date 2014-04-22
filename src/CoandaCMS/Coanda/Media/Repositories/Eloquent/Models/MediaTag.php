<?php namespace CoandaCMS\Coanda\Media\Repositories\Eloquent\Models;

use Eloquent, Coanda, App, Config, File;
use Carbon\Carbon;

/**
 * Class MediaTag
 * @package CoandaCMS\Coanda\Media\Repositories\Eloquent\Models
 */
class MediaTag extends Eloquent {

    /**
     * @var string
     */
    protected $table = 'mediatags';

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Media\Presenters\MediaTag';

    /**
     * @return mixed
     */
    public function media()
	{
		return $this->belongsToMany('CoandaCMS\Coanda\Media\Repositories\Eloquent\Models\Media');
	}
}