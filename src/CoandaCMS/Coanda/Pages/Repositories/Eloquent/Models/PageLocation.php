<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda, App, DB;
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
    protected $presenter = 'CoandaCMS\Coanda\Pages\Repositories\Eloquent\Presenters\PageLocation';

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
    public function childCount()
	{
		return $this->children()->whereHas('page', function ($query) { $query->where('is_trashed', '=', '0'); })->count();
	}

    /**
     * @return mixed
     */
    public function subTreeCount()
	{
		if (!$this->subTreeCount)
		{
			$path = $this->path == '' ? '/' : $this->path;

			// $this->subTreeCount = PageLocation::where('path', 'like', $path . $this->id . '/%')->count();
			$this->subTreeCount = $this->where('path', 'like', $path . $this->id . '/%')->count();
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

	public function getParentsAttribute()
	{
		return $this->parents();
	}

	public function getNameAttribute()
	{
		return $this->page->present()->name;
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


    /**
     * @param $query
     * @param $order
     * @return mixed
     */
    public function scopeOrderByPageName($query, $order)
	{
		$query->orderBy('pages.name', $order);

		return $query;
	}

    /**
     * @param $query
     * @param $order
     * @return mixed
     */
    public function scopeOrderByPageCreated($query, $order)
	{
		$query->orderBy('pages.created_at', $order);

		return $query;
	}

	public function scopeVisible($query)
	{
		$query->where( function ($query) {

			$query->where( function ($query) {

				$query->whereNull('pageversions.visible_from');
				$query->orWhere('pageversions.visible_from', '<', \DB::raw('NOW()'));
	
			});

			$query->where( function ($query) {

				$query->whereNull('pageversions.visible_to');
				$query->orWhere('pageversions.visible_to', '>', \DB::raw('NOW()'));

			});

		});
		
		return $query;
	}

	public function scopeNotHidden($query)
	{
		$query->where( function ($query) {

			$query->where('pageversions.is_hidden', '=', 0);
			$query->where('pageversions.is_hidden_navigation', '=', 0);

		});
		
		return $query;
	}

	public function scopeAttributeFilter($query, $filters)
	{
		$query->where( function ($query) use ($filters) {

			foreach ($filters as $filter)
			{
				$query->where( function ($query) use ($filter) {

					$nested_query = "select count(*)
							from pageattributes
							where page_version_id=pageversions.id
							and identifier='" . $filter['attribute'] . "'
							and attribute_data " . 
							$filter['type'] . ' ' .
							(is_numeric($filter['value']) ? $filter['value'] : ("'" . $filter['value'] . "'"));

					$nested_query = preg_replace('/\n/', '', $nested_query);

					$query->where(DB::raw('(' . $nested_query . ')'), '>=', 1);

				});
			}

		});


		return $query;
	}

	public function getAttributesAttribute()
	{
		return $this->page->renderAttributes($this);
	}

	public function breadcrumb($link_self = false)
	{
		$parents = $this->parents();

		$breadcrumb = [];

		if (count($parents) > 0)
		{
			foreach ($parents as $parent)
			{
				$breadcrumb[] = [
					'identifier' => 'pages:location-' . $parent->id,
					'layout_identifier' => 'pages:' . $parent->page->id,
					'url' => $parent->slug,
					'name' => $parent->present()->name
				];
			}
		}

		$breadcrumb[] = [
			'identifier' => 'pages:location-' . $this->id,
			'url' => $link_self ? $this->slug : false,
			'name' => $this->present()->name
		];

		return $breadcrumb;
	}

}