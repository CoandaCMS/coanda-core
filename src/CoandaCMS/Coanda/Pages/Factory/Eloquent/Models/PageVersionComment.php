<?php namespace CoandaCMS\Coanda\Pages\Factory\Eloquent\Models;

use Eloquent, Coanda, App;
use Carbon\Carbon;

/**
 * Class PageVersionComment
 * @package CoandaCMS\Coanda\Pages\Factory\Eloquent\Models
 */
class PageVersionComment extends Eloquent {

    use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Pages\Factory\Eloquent\Presenters\PageVersionComment';

    /**
     * @var string
     */
    protected $table = 'pageversioncomments';

    /**
     * @var array
     */
    protected $fillable = ['version_id', 'name', 'comment'];

    /**
     * @return mixed
     */
    public function version()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Factory\Eloquent\Models\PageVersion');
	}
}