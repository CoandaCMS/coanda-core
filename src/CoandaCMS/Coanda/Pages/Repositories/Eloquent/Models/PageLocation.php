<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda, App;
use Carbon\Carbon;

class PageLocation extends Eloquent {

	protected $table = 'pagelocations';

	protected $fillable = ['page_id', 'parent_page_id'];

	private $parents;
	private $subTreeCount;

	public function save(array $options = [])
	{
		// If we have a parent page, but no path, then we need to sort out the path
		if ($this->parent_page_id != 0 && $this->path == '')
		{
			$parent = $this->find($this->parent_page_id);

			if ($parent)
			{
				$this->path = ($parent->path == '' ? '/' : $parent->path) . $parent->id . '/';
			}
		}

		parent::save($options);
	}

	public function page()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page');
	}

	public function parent()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageLocation', 'parent_page_id');
	}

	public function children()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageLocation', 'parent_page_id');
	}

    public function subTreeCount()
	{
		if (!$this->subTreeCount)
		{
			$path = $this->path == '' ? '/' : $this->path;

			$this->subTreeCount = PageLocation::where('path', 'like', $path . $this->id . '/%')->count();
		}

		return $this->subTreeCount;
	}

    public function pathArray()
	{
		return explode('/', $this->path);
	}

    public function depth()
	{
		return count($this->pathArray());
	}

    public function getDepthAttribute()
	{
		return $this->depth();
	}

	public function parents()
	{
		if (!$this->parents)
		{
			$this->parents = new \Illuminate\Database\Eloquent\Collection;

			foreach ($this->pathArray() as $parent_id)
			{
				$parent = $this->find($parent_id);

				if ($parent)
				{
					$this->parents->add($parent);
				}
			}
		}

		return $this->parents;
	}

	public function getSlugAttribute()
	{
		$urlRepository = App::make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');

		try
		{
			return $urlRepository->findFor('pagelocation', $this->id)->slug;
		}
		catch(\CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound $exception)
		{
			return '';
		}
	}

	public function getBaseSlugAttribute()
	{
		if ($this->parent)
		{
			return $this->parent->slug;
		}

		return '';
	}

}