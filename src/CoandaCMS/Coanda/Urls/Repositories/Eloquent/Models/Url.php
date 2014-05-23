<?php namespace CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models;

/**
 * Class Url
 * @package CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models
 */
class Url extends \Illuminate\Database\Eloquent\Model {

    /**
     * @var string
     */
    protected $table = 'urls';

    /**
     * @var array
     */
    protected $fillable = ['slug', 'for', 'for_id'];

}