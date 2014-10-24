<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries;

use CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries\SubPageQuery;
use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;

class QueryHandler {

    /**
     * @var PageRepositoryInterface
     */
    private $repository;

    /**
     * @param PageRepositoryInterface $repository
     */
    public function __construct(PageRepositoryInterface $repository)
	{
		$this->repository = $repository;
	}

    /**
     * @param $parameters
     * @return mixed
     */
    public function subPages($parameters)
	{
		$query = new SubPageQuery($this->repository);

		return $query->execute($parameters);
	}

}