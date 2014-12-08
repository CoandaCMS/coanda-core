<?php namespace CoandaCMS\Coanda\Users\Presenters;

class ArchivedUser extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    /**
     * @return string
     */
    public function name()
    {
        return $this->model->name . ' *';
    }

    /**
     * @return mixed
     */
    public function email()
    {
        return $this->model->email;
    }

}