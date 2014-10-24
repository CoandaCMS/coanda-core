<?php namespace CoandaCMS\Coanda\Core;

use Eloquent;

class BaseEloquentModel extends Eloquent {

    public function format($attribute)
    {
        if (is_object($this->$attribute) && get_class($this->$attribute) == 'Carbon\Carbon')
        {
            return $this->$attribute->format('d/m/Y H:i');
        }

        return $this->$attribute;
    }
}
