<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Coanda;
use App;
use Lang;
use Carbon\Carbon;
use CoandaCMS\Coanda\Core\BaseEloquentModel;

class Page extends BaseEloquentModel {

    /**
     * @var array
     */
    protected $fillable = ['is_home', 'parent_page_id', 'type', 'created_by', 'edited_by', 'current_version'];

    /**
     * @var
     */
    private $cached_slug;

    /**
     * @var
     */
    private $pageType;
    /**
     * @var
     */
    private $currentVersionModel;

    /**
     * @var
     */
    private $renderedAttributes;

    /**
     * @var
     */
    private $parents;
    /**
     * @var
     */
    private $sub_tree_count;

    /**
     * @var
     */
    private $can_edit;
    /**
     * @var
     */
    private $can_view;
    /**
     * @var
     */
    private $can_remove;
    /**
     * @var
     */
    private $can_create;

    /**
     * @var string
     */
    protected $table = 'pages';

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
     * @param $name
     * @return string
     */
    public function getNameAttribute($name)
    {
        if ($name !== '')
        {
            return htmlspecialchars($name);
        }

        $generated_name = $this->pageType()->generateName($this->currentVersion());

        if ($generated_name !== '')
        {
            return htmlspecialchars($generated_name);
        }

        return \Lang::get('coanda::pages.page_name_not_set');
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
	 * Get the versions for this page
	 */
	public function versions()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion')->orderBy('version', 'desc');
	}

    /**
     * @param $version
     * @return mixed
     */
    public function getVersion($version)
	{
		return $this->versions()->whereVersion($version)->first();
	}

    /**
     * @return \CoandaCMS\Coanda\Pages\PageTypes\MissingPageType
     */
    public function pageType()
	{
        try
        {
            if (!$this->pageType)
            {
                if ($this->is_home)
                {
                    $this->pageType = Coanda::module('pages')->getHomePageType($this->type);
                }
                else
                {
                    $this->pageType = Coanda::module('pages')->getPageType($this->type);
                }
            }
        }
        catch (\CoandaCMS\Coanda\Pages\Exceptions\PageTypeNotFound $exception)
        {
            $this->pageType = new \CoandaCMS\Coanda\Pages\PageTypes\MissingPageType;
        }

        return $this->pageType;
	}

    /**
     * @return null
     */
    public function getTypeNameAttribute()
    {
        return $this->pageType()->name;
    }

    /**
     * @return null
     */
    public function getTypeIconAttribute()
    {
        return $this->pageType()->icon;
    }

    /**
     * @return mixed
     */
    private function getPageTypeDefinition()
	{
		return $this->pageType()->attributes();
	}

    /**
     * @return mixed
     */
    public function currentVersion()
	{
        if (!$this->currentVersionModel)
        {
            $this->currentVersionModel = $this->getVersion($this->current_version);
        }

        return $this->currentVersionModel;
	}

    /**
     * @return mixed
     */
    public function getStatusAttribute()
	{
		return $this->currentVersion()->status;
	}

    /**
     * @return string
     */
    public function getStatusTextAttribute()
    {
        if ($this->is_trashed)
        {
            return 'Trashed';
        }

        return Lang::get('coanda::pages.status_' . $this->status);
    }

    /**
     * @return bool
     */
    public function getIsVisibleAttribute()
	{
		$from = $this->visible_from;
		$to = $this->visible_to;

		if (!$from && !$to)
		{
			return true;
		}

		$now = Carbon::now(date_default_timezone_get());

		$is_visible = false;

		// Do we have a from and a to date?
		if ($from && $to)
		{
			if ($now->gt($from) && $now->lt($to))
			{
				$is_visible = true;
			}
		}
		else
		{
			// We have a from date
			if ($from)
			{
				if ($now->gt($from))
				{
					$is_visible = true;
				}
			}

			if ($to)
			{
				if ($now->lt($to))
				{
					$is_visible = true;
				}
			}
		}

		return $is_visible;
	}

    /**
     * @return mixed
     */
    public function getVisibleFromAttribute()
	{
		return $this->currentVersion()->visible_from;
	}

    /**
     * @return mixed
     */
    public function getVisibleToAttribute()
	{
		return $this->currentVersion()->visible_to;
	}

    /**
     * @return string
     */
    public function getVisibleDatesTextAttribute()
    {
        $visible_from = $this->visible_from;
        $visible_to = $this->visible_to;

        $text = '';

        if ($visible_from)
        {
            $text .= 'from ' . $visible_from;
        }

        if ($visible_to)
        {
            $text .= ' until ' . $visible_to;
        }

        return $text;
    }

    /**
     * @return mixed
     */
    public function getAttributesAttribute()
	{
		return $this->attributes();
	}

    /**
     * @return mixed
     */
    public function attributes()
	{
		return $this->currentVersion()->attributes()->with('version')->get();
	}

	/**
	 * Check if the status of the current version is a draft
	 * @return boolean
	 */
	public function getIsDraftAttribute()
	{
		return $this->currentVersion()->status == 'draft';
	}

	/**
	 * Check if the status of the current version is pending
	 * @return boolean
	 */
	public function getIsPendingAttribute()
	{
		return $this->currentVersion()->status == 'pending';
	}

    /**
     * @return mixed
     */
    public function getIsHiddenAttribute()
	{
		return $this->currentVersion()->is_hidden;
	}

    /**
     * @return mixed
     */
    public function getIsHiddenNavigationAttribute()
	{
		return $this->currentVersion()->is_hidden_navigation;
	}

    /**
     * @return mixed
     */
    public function getShowMetaAttribute()
	{
		return $this->pageType()->showMeta();
	}

    /**
     * @param $remote_id
     */
    public function setRemoteId($remote_id)
	{
		$this->remote_id = $remote_id;
		$this->save();
	}

    /**
     * @return mixed
     */
    public function availableTemplates()
	{
		return $this->pageType()->availableTemplates();
	}

    /**
     * @return \stdClass
     */
    public function renderAttributes()
	{
		if (!$this->renderedAttributes)
		{
			$this->renderedAttributes = new \stdClass;

			$this->renderCurrentAttributes($this->renderedAttributes);
			$this->addMissingAttributes($this->renderedAttributes);
		}

        return $this->renderedAttributes;
	}

    /**
     * @param $attributes
     * @return mixed
     */
    private function renderCurrentAttributes($attributes)
	{
		foreach ($this->attributes() as $attribute)
		{
			$attributes->{$attribute->identifier} = $attribute->render($this);
		}

		return $attributes;
	}

    /**
     *
     */
    private function addMissingAttributes()
	{		
		// Add any attributes which are on the definition, but not in the object..
		$attribute_definition_list = $this->getPageTypeDefinition();

		foreach ($attribute_definition_list as $attribute_definition_identifier => $attribute_definition)
		{
			if (!property_exists($this->renderedAttributes, $attribute_definition_identifier))
			{
				$this->renderedAttributes->{$attribute_definition_identifier} = isset($attribute_definition['default']) ? $attribute_definition['default'] : '';
			}
		}			
	}

    /**
     * @return mixed
     */
    public function getMetaPageTitleAttribute()
    {
        return $this->currentVersion()->meta_page_title;
    }

    /**
     * @return mixed
     */
    public function getMetaDescriptionAttribute()
    {
        return $this->currentVersion()->meta_description;
    }

    /**
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page', 'parent_page_id');
    }

    /**
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page', 'parent_page_id');
    }

    /**
     * @return mixed
     */
    public function childCount()
    {
        return $this->children()->where('is_trashed', '=', '0')->count();
    }

    /**
     * @return mixed
     */
    public function subTreeCount()
    {
        if (!$this->sub_tree_count)
        {
            $path = $this->path == '' ? '/' : $this->path;
            $this->sub_tree_count = $this->where('path', 'like', $path . $this->id . '/%')->count();
        }

        return $this->sub_tree_count;
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
    public function getDepthAttribute()
    {
        return count($this->pathArray());
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
        $this->parents = new \Illuminate\Support\Collection;

        foreach ($this->pathArray() as $parent_id)
        {
            $parent = $this->find($parent_id);

            if ($parent)
            {
                $this->parents->push($parent);
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
    public function getAllowsSubPagesAttribute()
    {
        return $this->pageType()->allowsSubPages();
    }

    /**
     * @return string
     */
    public function getFullPathAttribute()
    {
        return ($this->path == '' ? '/' : '') . $this->path . $this->id . '/';
    }

    /**
     * @return string
     */
    public function getSlugAttribute()
    {
        if (!$this->is_home)
        {
            if (!$this->cached_slug)
            {
                $urlRepository = App::make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');

                $url = $urlRepository->findFor('page', $this->id);

                if ($url)
                {
                    $this->cached_slug = $url->slug;
                }
            }

            return $this->cached_slug;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getParentSlugAttribute()
    {
        $parent = $this->parent;

        if ($parent)
        {
            return $parent->slug;
        }

        return '';
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
            $breadcrumb[] = [
                'identifier' => 'pages:page-' . $parent->id,
                'url' => $parent->slug,
                'name' => $parent->name,
            ];
        }

        $breadcrumb[] = [
            'identifier' => 'pages:page-' . $this->id,
            'url' => $link_self ? $this->slug : false,
            'name' => $this->name,
        ];

        return $breadcrumb;
    }

    public function getBreadcrumbTextAttribute()
    {
        $text = '';

        foreach ($this->parents() as $parent)
        {
            $text .= $parent->name . ' / ';
        }

        $text .= $this->name;

        return $text;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'parent_page_id' => $this->parent_page_id,
            'allows_sub_pages' => $this->pageType()->allowsSubPages(),
            'name' => $this->name,
            'breadcrumb_text' => $this->breadcrumb_text,
            'path' => $this->path,
        ];
    }

    /**
     * @return array
     */
    private function getPermissionData()
    {
        return [
            'page_id' => $this->id,
            'page_type' => $this->type,
        ];
    }

    /**
     * @return mixed
     */
    public function getCanRemoveAttribute()
    {
        if (!$this->can_remove)
        {
            $this->can_remove = Coanda::canView('pages', 'remove', $this->getPermissionData());
        }

        return $this->can_remove;
    }

    /**
     * @return mixed
     */
    public function getCanEditAttribute()
    {
        if (!$this->can_edit)
        {
            $this->can_edit = Coanda::canView('pages', 'edit', $this->getPermissionData());
        }

        return $this->can_edit;
    }

    /**
     * @return mixed
     */
    public function getCanViewAttribute()
    {
        if (!$this->can_view)
        {
            $this->can_view = Coanda::canView('pages', 'view', $this->getPermissionData());
        }

        return $this->can_view;
    }

    /**
     * @return mixed
     */
    public function getCanCreateAttribute()
    {
        if (!$this->can_create)
        {
            $this->can_create = Coanda::canView('pages', 'create', $this->getPermissionData());
        }

        return $this->can_create;
    }

    /**
     * @return mixed
     */
    public function getSubPageOrderTextAttribute()
    {
        $order_names = [
            'manual' => 'Manual',
            'alpha:asc' => 'Alpabetical (A-Z)',
            'alpha:desc' => 'Alpabetical (Z-A)',
            'created:desc' => 'Created date (Newest-Oldest)',
            'created:asc' => 'Created date (Oldest-Newest)',
        ];

        return $order_names[$this->sub_page_order];
    }

}