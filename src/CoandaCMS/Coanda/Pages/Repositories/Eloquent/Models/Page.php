<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda;

class Page extends Eloquent {

	private $pageType;

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

}