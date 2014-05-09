<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Coanda;

/**
 * Class LayoutRegion
 * @package CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models
 */
class LayoutRegion extends \Illuminate\Database\Eloquent\Model {

    /**
     * @var string
     */
    protected $table = 'layoutblockregions';

    /**
     * @var
     */
    private $layout;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block()
    {
        return $this->belongsTo('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlock', 'layout_block_id');
    }

    /**
     * @return mixed
     */
    public function layout()
    {
        if (!$this->layout)
        {
            $this->layout = Coanda::module('layout')->layoutByIdentifier($this->layout_identifier);
        }

        return $this->layout;
    }

    /**
     * @return mixed
     */
    public function getLayoutAttribute()
    {
        return $this->layout();
    }

    /**
     * @return mixed
     */
    public function region()
    {
        return $this->layout->region($this->region_identifier);
    }

    /**
     * @return mixed
     */
    public function getRegionAttribute()
    {
        return $this->region();
    }
}