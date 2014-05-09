<?php namespace CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models;

use Coanda, App;

/**
 * Class LayoutBlock
 * @package CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models
 */
class LayoutBlock extends \Illuminate\Database\Eloquent\Model {

    use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Layout\Presenters\LayoutBlock';

    /**
     * @var string
     */
    protected $table = 'layoutblocks';

    /**
     * @var
     */
    private $currentVersion;
    /**
     * @var
     */
    private $blockType;
    /**
     * @var
     */
    private $defaultRegions;

    /**
     *
     */
    public function delete()
    {
        foreach ($this->versions as $version)
        {
            $version->delete();
        }

        parent::delete();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function versions()
    {
    	return $this->hasMany('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutBlockVersion');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regions()
    {
        return $this->hasMany('CoandaCMS\Coanda\Layout\Repositories\Eloquent\Models\LayoutRegion');
    }

    /**
     * @return mixed
     */
    public function defaultRegions()
    {
        if (!$this->defaultRegions)
        {
            $this->defaultRegions = $this->regions()->whereModule('*')->get();
        }

        return $this->defaultRegions;
    }

    /**
     * @return mixed
     */
    public function currentVersion()
    {
    	if (!$this->currentVersion)
    	{
    		$this->currentVersion = $this->versions()->whereVersion($this->current_version)->first();
    	}

    	return $this->currentVersion;
    }

    /**
     * @return mixed
     */
    public function drafts()
    {
        return $this->versions()->whereStatus('draft')->get();
    }

    /**
     * @return mixed
     */
    public function blockType()
    {
        if (!$this->blockType)
        {
            $this->blockType = Coanda::module('layout')->blockTypeByIdentifier($this->type);
        }

        return $this->blockType;
    }

    /**
     * @return mixed
     */
    public function getAttributesAttribute()
    {
        return $this->currentVersion()->attributes()->get();
    }

    /**
     * @return mixed
     */
    public function getStatusAttribute()
    {
        return $this->currentVersion()->status;
    }

    /**
     * @return bool
     */
    public function getIsDraftAttribute()
    {
        return $this->currentVersion()->status == 'draft';
    }

    /**
     * @return array
     */
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