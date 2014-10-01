<?php namespace CoandaCMS\Coanda\Pages;

use Cache;
use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;
use Illuminate\Http\Request;

class PageQuery {

    /**
     * @var Repositories\PageRepositoryInterface
     */
    private $pageRepository;
    /**
     * @var Request
     */
    private $request;

    /**
     * @var
     */
    private $location_id;
    /**
     * @var
     */
    private $attribute_filters;
    /**
     * @var
     */
    private $limit;
    /**
     * @var
     */
    private $order_query;

    /**
     * @var bool
     */
    private $include_hidden = false;
    /**
     * @var bool
     */
    private $include_invisible = false;
    /**
     * @var bool
     */
    private $paginate = false;
	// private $cache_time = 0;

    /**
     * @param Repositories\PageRepositoryInterface $pageRepository
     * @param Request $request
     */
    public function __construct(PageRepositoryInterface $pageRepository, Request $request)
	{
		$this->pageRepository = $pageRepository;
		$this->request = $request;
	}

    /**
     * @param $location_id
     * @return $this
     */
    public function in($location_id)
	{
		$this->location_id = $location_id;

		return $this;
	}

    /**
     * @param $limit
     * @return $this
     */
    public function take($limit)
	{
		$this->limit = $limit;

		return $this;
	}

    /**
     * @return $this
     */
    public function includeHidden()
	{
		$this->include_hidden = true;

		return $this;
	}

    /**
     * @return $this
     */
    public function includeInvisible()
	{
		$this->include_invisible = true;

		return $this;
	}

    /**
     * @param $operator
     * @param $value
     * @return $this
     */
    public function order($operator, $value)
	{
		$this->order_query = [
			'operator' => $operator,
			'value' => $value
		];

		return $this;
	}

    /**
     * @param $attribute_identifier
     * @param $filter
     * @param string $query_type
     * @return $this
     */
    public function filter($attribute_identifier, $filter, $query_type = '=')
	{
		$this->attribute_filters[] = [
			'attribute' => $attribute_identifier,
			'value' => $filter,
			'type' => $query_type
		];

		return $this;
	}

    /**
     * @param $limit
     * @return $this
     */
    public function paginate($limit)
	{
		$this->paginate = true;
		$this->limit = $limit;

		return $this;
	}

    /**
     * @param $minutes
     * @return $this
     */
    public function cache($minutes)
	{
		$this->cache_time = $minutes;

		return $this;
	}

    /**
     * @return mixed
     */
    public function get()
	{
		$parameters = [
			'include_invisible' => $this->include_invisible,
			'include_hidden' => $this->include_hidden,
			'paginate' => $this->paginate,
			'attribute_filters' => $this->attribute_filters,
			'order_query' => $this->order_query
		];

		return $this->pageRepository->subPages($this->location_id, (int) $this->request->get('page', 1), $this->limit, $parameters);
	}

}