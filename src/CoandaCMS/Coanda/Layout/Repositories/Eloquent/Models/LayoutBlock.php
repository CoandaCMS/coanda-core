<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Coanda;

class LayoutBlock extends \Illuminate\Database\Eloquent\Model {

    use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    protected $presenter = 'CoandaCMS\Coanda\Layout\Presenters\LayoutBlock';

    protected $table = 'layoutblocks';

    private $currentVersion;
    private $blockType;

    public function delete()
    {
        foreach ($this->versions as $version)
        {
            $version->delete();
        }

        parent::delete();
    }

    public function versions()
    {
    	return $this->hasMany('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlockVersion');
    }

    public function currentVersion()
    {
    	if (!$this->currentVersion)
    	{
    		$this->currentVersion = $this->versions()->whereVersion($this->current_version)->first();
    	}

    	return $this->currentVersion;
    }

    public function blockType()
    {
        if (!$this->blockType)
        {
            $this->blockType = Coanda::module('layout')->blockTypeByIdentifier($this->type);
        }

        return $this->blockType;
    }

    public function getAttributesAttribute()
    {
        return $this->currentVersion()->attributes()->get();
    }

    public function getStatusAttribute()
    {
        return $this->currentVersion()->status;
    }

    public function getIsDraftAttribute()
    {
        return $this->currentVersion()->status == 'draft';
    }

}