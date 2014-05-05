<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Coanda;

class LayoutRegion extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'layoutblockregions';

    private $layout;

    public function block()
    {
        return $this->belongsTo('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlock', 'layout_block_id');
    }

    public function layout()
    {
        if (!$this->layout)
        {
            $this->layout = Coanda::module('layout')->layoutByIdentifier($this->layout_identifier);
        }

        return $this->layout;
    }

    public function getLayoutAttribute()
    {
        return $this->layout();
    }

    public function region()
    {
        return $this->layout->region($this->region_identifier);
    }

    public function getRegionAttribute()
    {
        return $this->region();
    }
}