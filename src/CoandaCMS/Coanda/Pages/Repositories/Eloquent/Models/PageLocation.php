<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use CoandaCMS\Coanda\Core\Presenters\PresentableTrait;
use CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound;
use Eloquent, Coanda, App, DB;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PageLocation
 * @package CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models
 */
class PageLocation extends Eloquent {

	use PresentableTrait;

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
     * @var
     */
    private $canEdit;
    /**
     * @var
     */
    private $canView;
    /**
     * @var
     */
    private $canRemove;
    /**
     * @var
     */
    private $canCreate;


    /**
     * @param array $options
     */
    public function save(array $options = [])
	{
		if ($this->path == '')
		{
            $this->setPath();
		}

		parent::save($options);
	}

    /**
     *
     */
    private function setPath()
    {
        $parent = $this->find($this->parent_page_id);

        if ($parent)
        {
            $this->path = ($parent->path == '' ? '/' : $parent->path) . $parent->id . '/';
        }
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
     * @return Collection
     */
    public function parents()
	{
		if (!$this->parents)
		{
            $this->generateParents();
		}

		return $this->parents;
	}

    /**
     *
     */
    private function generateParents()
    {
        $this->parents = new Collection;

        foreach ($this->pathArray() as $parent_id)
        {
            $parent = $this->find($parent_id);

            if ($parent)
            {
                $this->parents->add($parent);
            }
        }
    }

    /**
     * @return Collection
     */
    public function getParentsAttribute()
	{
		return $this->parents();
	}

    /**
     * @return mixed
     */
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
		catch (UrlNotFound $exception)
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

    /**
     * @param $query
     * @return mixed
     */
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

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotHidden($query)
	{
		$query->where( function ($query) {

			$query->where('pageversions.is_hidden', '=', 0);
			$query->where('pageversions.is_hidden_navigation', '=', 0);

		});
		
		return $query;
	}

    /**
     * @param $query
     * @param $filters
     * @return mixed
     */
    public function scopeAttributeFilter($query, $filters)
	{
		$query->where( function ($query) use ($filters) {

			foreach ($filters as $filter)
			{
				$query->where( function ($query) use ($filter) {

                    $value = $filter['value'];

                    if (!is_numeric($value))
                    {
                        $value = DB::connection()->getPdo()->quote($value);
                    }

					$nested_query = "select count(*)
							from pageattributes
							where page_version_id=pageversions.id
							and identifier='" . $filter['attribute'] . "'
							and attribute_data " . $filter['type'] . ' ' . $value;

					$nested_query = preg_replace('/\n/', '', $nested_query);

					$query->where(DB::raw('(' . $nested_query . ')'), '>=', 1);

				});
			}

		});

		return $query;
	}

    /**
     * @return mixed
     */
    public function getAttributesAttribute()
	{
		return $this->page->renderAttributes($this);
	}

    /**
     * @param bool $link_self
     * @return array
     */
    public function breadcrumb($link_self = false)
	{
		$parents = $this->parents();

		$breadcrumb = [];

        foreach ($parents as $parent)
        {
            $breadcrumb[] = $this->breadcrumbElement($parent);
        }

		$breadcrumb[] = [
			'identifier' => 'pages:location-' . $this->id,
			'url' => $link_self ? $this->slug : false,
			'name' => $this->present()->name,
			'_location_id' => $this->id,
		];

		return $breadcrumb;
	}

    /**
     * @param $location
     * @return array
     */
    private function breadcrumbElement($location)
    {
        return [
            'identifier' => 'pages:location-' . $location->id,
            'url' => $location->slug,
            'name' => $location->present()->name,
            '_location_id' => $location->id,
        ];
    }

    /**
     * @return array
     */
    private function getPermissionData()
	{
		return [
			'page_id' => $this->id,
			'page_location_id' => $this->id,
			'page_type' => $this->page->type,
		];
	}

    /**
     * @return mixed
     */
    public function getCanRemoveAttribute()
	{
		if (!$this->canRemove)
		{
			$this->canRemove = Coanda::canView('pages', 'remove', $this->getPermissionData());
		}

		return $this->canRemove;
	}

    /**
     * @return mixed
     */
    public function getCanEditAttribute()
	{
		if (!$this->canEdit)
		{
			$this->canEdit = Coanda::canView('pages', 'edit', $this->getPermissionData());
		}

		return $this->canEdit;
	}

    /**
     * @return mixed
     */
    public function getCanViewAttribute()
	{
		if (!$this->canView)
		{
			$this->canView = Coanda::canView('pages', 'view', $this->getPermissionData());
		}

		return $this->canView;
	}

    /**
     * @return mixed
     */
    public function getCanCreateAttribute()
	{
		if (!$this->canCreate)
		{
			$this->canCreate = Coanda::canView('pages', 'create', $this->getPermissionData());
		}

		return $this->canCreate;
	}

    /**
     * @return array
     */
    public function toArray()
	{
		return [
			'id' => $this->id,
			'parent_page_id' => $this->parent_page_id,
			'allows_sub_pages' => $this->page->pageType()->allowsSubPages(),
			'name' => $this->name,
			'path' => $this->path,
			'path_string' => $this->present()->path
		];
	}

    /**
     * @return mixed
     */
    public function getAllowsSubPagesAttribute()
    {
        return $this->page->pageType()->allowsSubPages();
    }
}