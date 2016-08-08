<?php namespace CoandaCMS\Coanda\Pages;

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
    private $page_id = 0;
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
    private $offset;

    /**
     * @var
     */
    private $order_query;

    private $sort_by;

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

    /**
     * @var array
     */
    private $include_page_types = [];
    /**
     * @var array
     */
    private $exclude_page_types = [];

    /**
     * @var int
     */
    private $page;

    /**
     * @param Repositories\PageRepositoryInterface $pageRepository
     * @param Request $request
     */
    public function __construct(PageRepositoryInterface $pageRepository, Request $request)
	{
		$this->pageRepository = $pageRepository;
		$this->request = $request;

        $this->page = (int) $this->request->get('page', 1);
	}

    /**
     * @param $page_id
     * @return $this
     */
    public function in($page_id)
	{
		$this->page_id = $page_id;

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
     * @param $offset
     * @return $this
     */
    public function skip($offset)
    {
        $this->offset = $offset;

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

    public function sortBy($field, $order)
    {
        $this->sort_by = [
            'field' => $field,
            'order' => $order
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
     * @param $page_types
     * @return $this
     */
    public function includePageTypes($page_types)
    {
        $this->include_page_types = $page_types;

        return $this;
    }

    /**
     * @param $page_types
     * @return $this
     */
    public function excludePageTypes($page_types)
    {
        $this->exclude_page_types = $page_types;

        return $this;
    }

    /**
     * @param $limit
     * @param bool $page
     * @return $this
     */
    public function paginate($limit, $page = false)
	{
		$this->paginate = true;
		$this->limit = $limit;

        if ($page)
        {
            $this->page = $page;
        }

		return $this;
	}

    /**
     * @return array
     */
    private function buildParameters()
    {
        return [
            'include_invisible' => $this->include_invisible,
            'include_hidden' => $this->include_hidden,
            'paginate' => $this->paginate,
            'attribute_filters' => $this->attribute_filters,
            'include_page_types' => $this->include_page_types,
            'exclude_page_types' => $this->exclude_page_types,
            'order_query' => $this->order_query,
            'sort_by' => $this->sort_by,
            'offset' => $this->offset
        ];
    }

    /**
     * @return mixed
     */
    public function get()
	{
        return $this->pageRepository->subPages($this->page_id, $this->page, $this->limit, $this->buildParameters());
	}

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->pageRepository->subPageCount($this->page_id, $this->buildParameters());
    }

    /**
     * @return mixed
     */
    public function first()
    {
        $list = $this->pageRepository->subPageList($this->page_id, $this->limit, $this->buildParameters());

        return $list->first();
    }

}