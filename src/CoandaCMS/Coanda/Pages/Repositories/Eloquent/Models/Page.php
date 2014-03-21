<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda;

class Page extends Eloquent {

	private $pageType;
	private $currentVersion;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pages';

	public function versions()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion');
	}

	public function pageType()
	{
		if (!$this->pageType)
		{
			$this->pageType = Coanda::getPageType($this->type);
		}

		return $this->pageType;
	}

	public function typeName()
	{
		return $this->pageType()->name;
	}

	public function getTypeNameAttribute()
	{
		return $this->typeName();
	}

	public function currentVersion()
	{
		if (!$this->currentVersion)
		{
			$this->currentVersion = $this->versions()->whereVersion($this->current_version)->first();
		}

		return $this->currentVersion;
	}

	public function getStatusAttribute()
	{
		return $this->currentVersion()->status;
	}

	public function getAttributesAttribute()
	{
		return $this->currentVersion()->attributes()->get();
	}

}