<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda, App;
use Carbon\Carbon;

/**
 * Class PageLocation
 * @package CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models
 */
class PageLocation extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Pages\Presenters\PageLocation';

    /**
     * @var string
     */
    protected $table = 'pagelocations';

    /**
     * @var array
     */
    protected $fillable = ['page_id', 'parent_page_id'];

    /**
     * @var
     */
    private $parents;
    /**
     * @var
     */
    private $subTreeCount;

    /**
     * @param array $options
     */
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

    /**
     * @return mixed
     */
    public function page()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page');
	}

    /**
     * @return mixed
     */
    public function parent()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageLocation', 'parent_page_id');
	}

    /**
     * @return mixed
     */
    public function children()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageLocation', 'parent_page_id');
	}

    /**
     * @return mixed
     */
    public function subTreeCount()
	{
		if (!$this->subTreeCount)
		{
			$path = $this->path == '' ? '/' : $this->path;

			$this->subTreeCount = PageLocation::where('path', 'like', $path . $this->id . '/%')->count();
		}

		return $this->subTreeCount;
	}

    /**
     * @return array
     */
    public function pathArray()
	{
		return explode('/', $this->path);
	}

    /**
     * @return int
     */
    public function depth()
	{
		return count($this->pathArray());
	}

    /**
     * @return int
     */
    public function getDepthAttribute()
	{
		return $this->depth();
	}

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
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

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getBaseSlugAttribute()
	{
		if ($this->parent)
		{
			return $this->parent->slug;
		}

		return '';
	}


	public function scopeOrderByPageName($query, $order)
	{
		$query->join('pages', 'pagelocations.page_id', '=', 'pages.id');
		$query->orderBy('pages.name', $order);

		return $query;
	}

	public function scopeOrderByPageCreated($query, $order)
	{
		$query->join('pages', 'pagelocations.page_id', '=', 'pages.id');
		$query->orderBy('pages.created_at', $order);

		return $query;
	}

}