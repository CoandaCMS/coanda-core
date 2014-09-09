<?php namespace CoandaCMS\Coanda\Pages;

use CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface;
use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;
use CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface;

class PageManager {

    /**
     * @var Repositories\PageRepositoryInterface
     */
    private $repository;
    /**
     * @var \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface
     */
    private $history;
    /**
     * @var \CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface
     */
    private $users;
    /**
     * @var
     */
    private $current_user_id;

    /**
     * @param PageRepositoryInterface $pageRepository
     * @param HistoryRepositoryInterface $historyRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(PageRepositoryInterface $pageRepository, HistoryRepositoryInterface $historyRepository, UserRepositoryInterface $userRepository)
	{
		$this->repository = $pageRepository;
		$this->history = $historyRepository;
		$this->users = $userRepository;

		$this->current_user_id = $this->users->currentUser()->id;
	}

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    private function callModuleMethod($method, $parameters)
	{
		$coanda = \App::make('coanda');

		return call_user_func_array([$coanda->pages(), $method], $parameters);
	}

    /**
     * @param $id
     * @param int $limit
     * @return mixed
     */
    private function getHistory($id, $limit = 10)
	{
		return $this->history->getPaginated('pages', $id, $limit);
	}

    /**
     * @param $what_happened
     * @param $identifier
     * @param $data
     */
    private function logHistory($what_happened, $identifier, $data)
	{
		$this->history->add('pages', $identifier, $this->current_user_id, $what_happened, $data);
	}

    /**
     * @return mixed
     */
    public function getHomePage()
	{
		return $this->repository->getHomePage();
	}

    /**
     * @param $id
     * @return mixed
     */
    public function getLocation($id)
	{
		return $this->repository->locationById($id);
	}

    /**
     * @param $current_page
     * @param $per_page
     * @return mixed
     */
    public function getTopLevelPages($current_page, $per_page)
	{
		return $this->getAdminSubLocations(0, $current_page, $per_page);
	}

    /**
     * @param $parent_location_id
     * @param $current_page
     * @param $per_page
     * @return mixed
     */
    public function getAdminSubLocations($parent_location_id, $current_page, $per_page)
	{
		return $this->repository->subPages($parent_location_id, $current_page, $per_page, [
			'include_drafts' => true,
			'include_invisible' => true,
			'include_hidden' => true
		]);
	}

    /**
     * @param $orders
     */
    public function updateLocationOrders($orders)
	{
		foreach ($orders as $location_id => $new_order)
		{
			$this->repository->updateLocationOrder($location_id, $new_order);
		}
	}

    /**
     * @param $id
     * @return mixed
     */
    public function getPage($id)
	{
		return $this->repository->find($id);
	}

    /**
     * @param $page_type
     * @param $parent_location_id
     * @return mixed
     */
    public function createNewPage($page_type, $parent_location_id)
	{
		$type = $this->callModuleMethod('getPageType', [$page_type]);

		return $this->repository->create($type, $this->current_user_id, $parent_location_id);
	}

    /**
     * @param $page_id
     * @param $base_version_number
     * @return mixed
     */
    public function createNewVersionForPage($page_id, $base_version_number)
	{
		return $this->repository->createNewVersion($page_id, $this->current_user_id, $base_version_number);
	}

    /**
     * @param $page_id
     * @param $version_number
     * @return mixed
     */
    public function getVersionForPage($page_id, $version_number)
	{
		return $this->repository->getDraftVersion($page_id, $version_number);
	}

    /**
     * @param $page_id
     * @param $version_number
     */
    public function removeDraftVersion($page_id, $version_number)
	{
		$version = $this->getVersionForPage($page_id, $version_number);

		$this->logHistory('discard_version', $version->page->id, ['version' => $version->version]);

		$this->repository->discardDraftVersion($version, $this->current_user_id);
	}

    /**
     * @param $page_id
     * @param $version_number
     * @param $input
     */
    public function saveDraftVersion($page_id, $version_number, $input)
	{
		$version = $this->getVersionForPage($page_id, $version_number);

		$this->repository->saveDraftVersion($version, $input);
	}

    /**
     * @param $version_id
     * @param $slug_id
     */
    public function removeSlugFromVersion($version_id, $slug_id)
	{
		$this->repository->removeVersionSlug($version_id, $slug_id);
	}

    /**
     * @param $ids
     * @return mixed
     */
    public function getPages($ids)
	{
		return $this->repository->findByIds($ids);
	}

    /**
     * @param $ids
     * @param bool $permanent
     * @return mixed
     */
    public function deletePages($ids, $permanent = false)
	{
		return $this->repository->deletePages($ids, $permanent);
	}

    /**
     * @param $id
     * @param int $limit
     * @return mixed
     */
    public function pageHistory($id, $limit = 10)
	{
		return $this->getHistory($id, $limit);
	}

    /**
     * @param $id
     * @return mixed
     */
    public function pageContributors($id)
	{
		return $this->history->users('pages', $id);
	}

    /**
     * @param $page_id
     * @param bool $user_id
     * @return mixed
     */
    public function draftsForUser($page_id, $user_id = false)
	{
		if (!$user_id)
		{
			$user_id = $this->current_user_id;
		}

		return $this->repository->draftsForUser($page_id, $user_id);
	}

}
