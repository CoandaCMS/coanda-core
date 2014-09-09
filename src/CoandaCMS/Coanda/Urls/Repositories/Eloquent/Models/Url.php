<?php namespace CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models;

use Eloquent;

/**
 * Class Url
 * @package CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models
 */
class Url extends Eloquent {

    /**
     * @var string
     */
    protected $table = 'urls';

    /**
     * @var array
     */
    protected $fillable = ['slug', 'for', 'for_id'];

}