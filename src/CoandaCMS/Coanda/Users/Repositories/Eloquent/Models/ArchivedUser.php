<?php namespace CoandaCMS\Coanda\Users\Repositories\Eloquent\Models;

use Eloquent;

class ArchivedUser extends Eloquent {

    use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Users\Presenters\ArchivedUser';

    /**
     * @var string
     */
    protected $table = 'users_archived';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'email'];
}