<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use CoandaCMS\Coanda\Core\BaseEloquentModel;

class PageVersionComment extends BaseEloquentModel {

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