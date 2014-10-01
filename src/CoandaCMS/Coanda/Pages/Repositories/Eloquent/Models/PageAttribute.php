<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda;

use CoandaCMS\Coanda\Core\Attributes\Exceptions\AttributeTypeNotFound;

class PageAttribute extends Eloquent {

    /**
     * @var bool
     */
    public $timestamps = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pageattributes';

    /**
     * @var array
     */
    protected $fillable = ['page_version_id', 'identifier', 'type', 'order', 'attribute_data'];

    /**
     *
     */
    public function delete()
	{
		$parameters = [
			'attribute_id' => $this->id,
			'page_id' => $this->page()->id,
			'version_number' => $this->version->version
		];

		$this->type()->delete($parameters);

		parent::delete();
	}

	/**
	 * Returns the version for this attribute
	 * @return CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion
	 */
	public function version()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion', 'page_version_id');
	}

	/**
	 * Returns the page for this attribute
	 * @return CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page
	 */
	public function page()
	{
		return $this->version->page;
	}

	/**
	 * Returns the type for the page
	 * @return CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page
	 */
	public function pageType()
	{
		return $this->page()->pageType();
	}

	/**
	 * Returns the name for this attribute, via the type definition
	 * @return string
	 */
	public function name()
	{
		$attributes = $this->pageType()->attributes();

		return isset($attributes[$this->identifier]) ? $attributes[$this->identifier]['name'] : $this->identifier;
	}

	/**
	 * Returns if the attribute is required
	 * @return boolean
	 */
	public function isRequired()
	{
		$attributes = $this->pageType()->attributes();

		return isset($attributes[$this->identifier]['required']) ? $attributes[$this->identifier]['required'] : false;
	}

    /**
     * @return bool
     */
    public function generatesSlug()
	{
		$attributes = $this->pageType()->attributes();

		if (!isset($attributes[$this->identifier]))
		{
			return false;
		}

		$attribute_definition = $attributes[$this->identifier];

		return isset($attribute_definition['generates_slug']) && $attribute_definition['generates_slug'] == true;
	}

    /**
     * @return bool
     */
    public function getGeneratesSlugAttribute()
	{
		return $this->generatesSlug();
	}

	/**
	 * Calls the isRequired method
	 * @return boolean
	 */
	public function getIsRequiredAttribute()
	{
		return $this->isRequired();
	}

	/**
	 * Calls the name method
	 * @return string
	 */
	public function getNameAttribute()
	{
		return $this->name();
	}

    /**
     * @return mixed
     */
    public function type()
	{
		try
		{
			return Coanda::getAttributeType($this->type);	
		}
		catch (AttributeTypeNotFound $exception)
		{
			return false;
		}
	}

	/**
	 * Get the data from the type
	 * @return mixed
	 */
	public function typeData()
	{
		// Let the type do whatever with the attribute to return the data required...
		$parameters = [
			'attribute_id' => $this->id,
			'page_id' => $this->page()->id,
			'version_number' => $this->version->version
		];

		$attribute_type = $this->type();

		if ($attribute_type)
		{
			return $attribute_type->data($this->attribute_data, $parameters);	
		}

		return false;
	}

	public function getContentAttribute()
	{
		return $this->typeData();
	}

	/**
	 * Calls the typeData method
	 * @return array
	 */
	public function getTypeDataAttribute()
	{
		return $this->typeData();
	}

	/**
	 * Stores the data provided
	 * @param  array $data
	 * @return void
	 */
	public function store($data, $data_key)
	{
		// Let the type do whatever with the attribute to return the data required...
		$parameters = [
			'page_id' => $this->page()->id,
			'version_number' => $this->version->version,
			'data_key' => $data_key
		];

		$attribute_type = $this->type();

		if ($attribute_type)
		{
			// Let the type class validate/manipulate the data...
			$this->attribute_data = $attribute_type->store($data, $this->isRequired(), $this->name(), $parameters);
		}

		$this->save();
	}

    /**
     * @param $action
     * @param $data
     */
    public function handleAction($action, $data)
	{
		// Let the type do whatever with the attribute to return the data required...
		$parameters = [
			'attribute_id' => $this->id,
			'attribute_identifier' => $this->identifier,
			'page_id' => $this->page()->id,
			'version_number' => $this->version->version
		];

		$attribute_type = $this->type();

		if ($attribute_type)
		{
			$action_result = $attribute_type->handleAction($action, $data, $parameters);

			if ($action_result)
			{
				$this->attribute_data = $action_result;
				$this->save();
			}
		}
	}

    /**
     * @param bool $page
     * @param bool $pagelocation
     * @param bool $indexing
     * @return mixed
     */
    public function render($page = false, $pagelocation = false, $indexing = false)
	{
		$parameters = [
			'attribute_id' => $this->id,
			'page_id' => $this->page()->id,
			'version_number' => $this->version->version,
			'page' => $page,
			'pagelocation' => $pagelocation,
			'indexing' => $indexing
		];

		$attribute_type = $this->type();

		if ($attribute_type)
		{
			return $attribute_type->render($this->typeData(), $parameters);	
		}

		return false;
	}

	public function getDefinitionAttribute()
	{
        $definition = $this->pageType()->attributes();

        return isset($definition[$this->identifier]) ? $definition[$this->identifier] : false;
	}
}