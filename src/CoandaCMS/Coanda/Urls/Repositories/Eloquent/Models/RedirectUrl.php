<?php namespace CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RedirectUrl
 * @package CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models
 */
class RedirectUrl extends Model {

    /**
     * @var string
     */
    protected $table = 'redirecturls';

    /**
     * @var array
     */
    protected $fillable = ['destination', 'redirect_type'];
    /**
     * @var
     */
    private $counter;

    /**
     *
     */
    public function addHit()
    {
    	$this->counter = $this->counter + 1;
    	$this->save();
    }

    /**
     * @return string
     */
    public function fromUrl()
    {
    	$urlRepository = \App::make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');

    	$url = $urlRepository->findFor('redirecturl', $this->id);

    	if ($url)
    	{
    		return $url->slug;
    	}

    	return '';
    }

    /**
     * @return string
     */
    public function getFromUrlAttribute()
    {
    	return $this->fromUrl();
    }

}