<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Coanda, App;

class LayoutBlock extends \Illuminate\Database\Eloquent\Model {

    use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    protected $presenter = 'CoandaCMS\Coanda\Layout\Presenters\LayoutBlock';

    protected $table = 'layoutblocks';

    private $currentVersion;
    private $blockType;
    private $defaultRegions;

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

    public function regions()
    {
        return $this->hasMany('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutRegion');
    }

    public function defaultRegions()
    {
        if (!$this->defaultRegions)
        {
            $this->defaultRegions = $this->regions()->whereModule('*')->get();
        }

        return $this->defaultRegions;
    }

    public function currentVersion()
    {
    	if (!$this->currentVersion)
    	{
    		$this->currentVersion = $this->versions()->whereVersion($this->current_version)->first();
    	}

    	return $this->currentVersion;
    }

    public function drafts()
    {
        return $this->versions()->whereStatus('draft')->get();
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

    public function availableRegions()
    {
        $regions = [];
        $layouts = Coanda::module('layout')->layouts();

        $layoutRepository = App::make('CoandaCMS\Coanda\Layout\Repositories\LayoutBlockRepositoryInterface');

        foreach ($layouts as $layout)
        {
            foreach ($layout->regions() as $region)
            {
                if (!$layoutRepository->checkBlockIsDefaultInRegion($this->id, $layout->identifier(), $region->identifier()))
                {
                    $regions[] = [
                        'name' => $layout->name() . '/' . $region->name(),
                        'identifier' => $layout->identifier() . '/' . $region->identifier()
                    ];                    
                }
            }
        }

        return $regions;
    }
}