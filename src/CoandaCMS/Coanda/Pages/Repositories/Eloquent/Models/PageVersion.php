<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Coanda;
use CoandaCMS\Coanda\Users\Exceptions\UserNotFound;
use Illuminate\Support\Collection;
use Lang;
use CoandaCMS\Coanda\Core\BaseEloquentModel;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;
use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute as PageAttributeModel;

class PageVersion extends BaseEloquentModel {

    /**
     * @var array
     */
    protected $dates = ['visible_from', 'visible_to'];

    /**
     * @var array
     */
    protected $fillable = ['parent_page_id', 'page_id', 'slug', 'version', 'status', 'created_by', 'edited_by', 'meta_page_title', 'meta_description', 'visible_from', 'visible_to', 'template_identifier', 'layout_identifier', 'is_hidden', 'is_hidden_navigation'];

	/**
	 * @var string
     */
	protected $table = 'pageversions';

	/**
	 * @param array $options
     */
	public function save(array $options = [])
	{
		if (!$this->preview_key)
		{
			$this->preview_key = md5($this->page_id . '-' . $this->version . '-' . time());
		}

		parent::save($options);
	}

	/**
	 *
     */
	public function delete()
	{
		foreach ($this->attributes()->get() as $attribute)
		{
			$attribute->delete();
		}

		parent::delete();
	}

	/**
	 * @return mixed
     */
	public function attributes()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageAttribute')->orderBy('order', 'asc');
	}

	/**
	 * @return mixed
     */
	public function comments()
	{
		return $this->hasMany('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersionComment', 'version_id')->orderBy('created_at', 'desc');;
	}

	/**
	 * @param $identifier
	 * @return mixed
     */
	public function getAttributeByIdentifier($identifier)
	{
		return $this->attributes()->whereIdentifier($identifier)->first();
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
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page', 'parent_page_id');
	}

	/**
	 * @return Collection
	 */
	public function parents()
	{
		if ($this->parent)
		{
			$parents = $this->parent->parents;
			$parents->push($this->parent);

			return $parents;
		}

		return new Collection([]);
	}

	/**
	 *
     */
	public function checkAttributes()
	{
		$pageType = $this->page->pageType();
		$attribute_definition_list = $pageType->attributes();

		$existing_attributes = [];

		// Do all the existing attributes still exist on the definition?
		foreach ($this->attributes()->get() as $attribute)
		{
			if (!array_key_exists($attribute->identifier, $attribute_definition_list))
			{
				// This attribute is no longer in the definition, so lets remove it
				$attribute->delete();
			}
			else
			{
				$existing_attributes[$attribute->identifier] = $attribute;
			}
		}

		$index = 1;

		foreach ($attribute_definition_list as $attribute_identifier => $definition)
		{
			if (!in_array($attribute_identifier, array_keys($existing_attributes)))
			{
				// We need to add this new attribute
				$page_attribute_type = Coanda::getAttributeType($definition['type']);

				$new_attribute = new PageAttributeModel;

				$new_attribute->type = $page_attribute_type->identifier();
				$new_attribute->identifier = $attribute_identifier;
				$new_attribute->order = $index;

				// Add the default value...
				if (isset($definition['default']))
				{
					try
					{
						$new_attribute->attribute_data = $page_attribute_type->store($definition['default'], false, '');
					}
					catch (AttributeValidationException $exception)
					{
						// Do nothing...
					}
				}

				$this->attributes()->save($new_attribute);
			}
			else
			{
				// Update the order on the existing attribute (in case we have added another one)
				$existing_attributes[$attribute_identifier]->order = $index;
				$existing_attributes[$attribute_identifier]->save();				
			}

			$index ++;
		}
	}

	/**
	 * @return bool
     */
	public function layout()
    {
        if ($this->layout_identifier !== '')
        {
            return Coanda::module('layout')->layoutByIdentifier($this->layout_identifier);
        }

        return false;
    }

	/**
	 * @return bool
     */
	public function getLayoutAttribute()
    {
        return $this->layout();
    }

	/**
	 * @return mixed
     */
	public function availableTemplates()
    {
    	return $this->page->availableTemplates();
    }

	/**
	 * @return string
     */
	public function getFullSlugAttribute()
    {
        $parent_slug = $this->page->parent_slug;

        return (($parent_slug !== '') ? ($parent_slug . '/') : '') . $this->slug;
    }

	/**
	 * @return mixed
     */
	public function getPendingDisplayTextAttribute()
    {
        $publish_handler = Coanda::pages()->getPublishHandler($this->publish_handler);

        return $publish_handler->display($this->publish_handler_data);
    }

	/**
	 * @return string
     */
	public function getPreviewUrlAttribute()
    {
        return 'pages/preview/' . $this->preview_key;
    }

	/**
	 * @return string
	 */
	public function getStatusTextAttribute()
	{
		return Lang::get('coanda::pages.status_' . $this->status);
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
	 * @return string
     */
	public function getCreatorNameAttribute()
	{
		$user = $this->creator();

		if ($user)
		{
			return $user->full_name;
		}

		return 'Unknown';
	}

	/**
	 * @return mixed
     */
	private function creator()
	{
		$user_manager = \App::make('CoandaCMS\Coanda\Users\UserManager');

		try
		{
			$user = $user_manager->getUserById($this->created_by);
		}
		catch (UserNotFound $exception)
		{
			$user = $user_manager->getArchivedUserById($this->created_by);
		}

		return $user;
	}
}