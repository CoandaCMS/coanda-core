<?php namespace CoandaCMS\Coanda\History\Repositories\Eloquent\Models;

use CoandaCMS\Coanda\Users\Exceptions\UserNotFound;
use Eloquent, Coanda, App;

class HistoryDigestSubscriber extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'history_digest_subscribers';

    protected $fillable = ['user_id'];

    /**
     * @return mixed
     */
    public function user()
    {
        $userRepository = \App::make('CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface');

        try
        {
            return $userRepository->find($this->user_id);
        }
        catch (UserNotFound $exception)
        {
            return false;
        }
    }

    public function getEmailAttribute()
    {
        $user = $this->user();

        if ($user)
        {
            return $user->email;
        }

        return false;
    }
}