<?php namespace CoandaCMS\Coanda\Users\Repositories\Eloquent\Models;

use Eloquent;

/**
 * Class UserGroup
 * @package CoandaCMS\Coanda\Users\Repositories\Eloquent\Models
 */
class UserGroup extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Users\Presenters\UserGroup';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_groups';

    /**
     * @return mixed
     */
    public function users()
	{
		return $this->belongsToMany('CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\User');
	}

    /**
     * @return mixed
     */
    public function getAccessListAttribute()
	{
		return json_decode($this->permissions, true);
	}
}