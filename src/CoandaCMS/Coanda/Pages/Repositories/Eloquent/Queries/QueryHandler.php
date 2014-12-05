<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries;

use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries\SubPageQuery;
use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;

class QueryHandler {

    /**
     * @var PageRepositoryInterface
     */
    private $repository;

	/**
	 * @var SubPageQuery
     */
	private $subPageQuery;

    /**
     * @param PageRepositoryInterface $repository
     */
    public function __construct(PageRepositoryInterface $repository, SubPageQuery $subPageQuery)
	{
		$this->repository = $repository;
		$this->subPageQuery = $subPageQuery;
	}

    /**
     * @param $parameters
     * @return mixed
     */
    public function subPages($parameters)
	{
		return $this->subPageQuery->execute($parameters);
	}

	/**
	 * @param $parameters
	 * @return mixed
	 */
	public function subPageCount($parameters)
	{
		return $this->subPageQuery->executeCount($parameters);
	}

	/**
	 * @param $parameters
	 * @return mixed
     */
	public function subPageList($parameters)
	{
		return $this->subPageQuery->executeList($parameters);
	}

}