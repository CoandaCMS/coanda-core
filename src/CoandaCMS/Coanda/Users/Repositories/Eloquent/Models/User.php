<?php namespace CoandaCMS\Coanda\Users\Repositories\Eloquent\Models;

use Eloquent;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * Class User
 * @package CoandaCMS\Coanda\Users\Repositories\Eloquent\Models
 */
class User extends Eloquent implements UserInterface, RemindableInterface {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Users\Presenters\User';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}


	public function getRememberToken()
	{
	    return $this->remember_token;
	}

	public function setRememberToken($value)
	{
	    $this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
	    return 'remember_token';
	}	

    /**
     * @return mixed
     */
    public function groups()
	{
		return $this->belongsToMany('CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\UserGroup');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnassignedGroupsAttribute()
	{
		return $this->unassigned_groups();
	}

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function unassigned_groups()
	{
		$unassigned_groups = new \Illuminate\Database\Eloquent\Collection;
		$groups = UserGroup::get();

		$current_groups = $this->groups->lists('id');

		foreach ($groups as $group)
		{
			if (!in_array($group->id, $current_groups))
			{
				$unassigned_groups->add($group);
			}
		}

		return $unassigned_groups;
	}

    /**
     * @return string
     */
    public function avatar()
	{
		return 'http://www.gravatar.com/avatar/' . md5($this->email) . '?d=mm';
	}

    /**
     * @return string
     */
    public function getAvatarAttribute()
	{
		return $this->avatar();
	}
}