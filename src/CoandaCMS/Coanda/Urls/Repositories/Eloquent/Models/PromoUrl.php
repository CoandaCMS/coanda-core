<?php namespace CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models;

/**
 * Class PromoUrl
 * @package CoandaCMS\Coanda\Urls\Repositories\Eloquent\Models
 */
class PromoUrl extends \Illuminate\Database\Eloquent\Model {

    /**
     * @var string
     */
    protected $table = 'promourls';

    /**
     * @var array
     */
    protected $fillable = ['destination'];

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

    	$url = $urlRepository->findFor('promourl', $this->id);

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