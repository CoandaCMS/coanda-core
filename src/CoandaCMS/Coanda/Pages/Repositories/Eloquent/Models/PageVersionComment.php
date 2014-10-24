<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent;
use Coanda;
use App;

class PageVersionComment extends Eloquent {

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
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion');
	}
}