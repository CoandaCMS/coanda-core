<?php namespace CoandaCMS\Coanda\Pages;

class PageManager {

	private $repository;
    private $history;
    private $users;
    private $current_user_id;

    public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository, \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface $historyRepository, \CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface $userRepository)
	{
		$this->repository = $pageRepository;
		$this->history = $historyRepository;
		$this->users = $userRepository;

		$this->current_user_id = $this->users->currentUser()->id;
	}

	private function callModuleMethod($method, $parameters)
	{
		$coanda = \App::make('coanda');

		return call_user_func_array([$coanda->pages(), $method], $parameters);
	}

	private function getHistory($id, $limit = 10)
	{
		return $this->history->getPaginated('pages', $id, $limit);
	}

	private function logHistory($what_happened, $identifier, $data)
	{
		$this->history->add('pages', $identifier, $this->current_user_id, $what_happened, $data);
	}

	public function getHomePage()
	{
		return $this->repository->getHomePage();
	}

	public function getLocation($id)
	{
		return $this->repository->locationById($id);
	}

	public function getTopLevelPages($current_page, $per_page)
	{
		return $this->getAdminSubLocations(0, $current_page, $per_page);
	}

	public function getAdminSubLocations($parent_location_id, $current_page, $per_page)
	{
		return $this->repository->subPages($parent_location_id, $current_page, $per_page, [
			'include_drafts' => true,
			'include_invisible' => true,
			'include_hidden' => true
		]);
	}

	public function updateLocationOrders($orders)
	{
		foreach ($orders as $location_id => $new_order)
		{
			$this->repository->updateLocationOrder($location_id, $new_order);

			// $this->logHistory('order_changed', $location_id, ['new_order' => $new_order]);
		}
	}

	public function getPage($id)
	{
		return $this->repository->find($id);
	}

	public function createNewPage($page_type, $parent_location_id)
	{
		$type = $this->callModuleMethod('getPageType', [$page_type]);

		return $this->repository->create($type, $this->current_user_id, $parent_location_id);
	}

	public function createNewVersionForPage($page_id, $base_version_number)
	{
		return $this->repository->createNewVersion($page_id, $this->current_user_id, $base_version_number);
	}

	public function getVersionForPage($page_id, $version_number)
	{
		return $this->repository->getDraftVersion($page_id, $version_number);
	}

	public function removeDraftVersion($page_id, $version_number)
	{
		$version = $this->getVersionForPage($page_id, $version_number);

		$this->repository->discardDraftVersion($version, $this->current_user_id);
		$this->logHistory($version->page->id, 'discard_version', ['version' => $version->version]);
	}

	public function saveDraftVersion($page_id, $version_number, $input)
	{
		$version = $this->getVersionForPage($page_id, $version_number);

		$this->repository->saveDraftVersion($version, $input);
	}

	public function removeSlugFromVersion($version_id, $slug_id)
	{
		$this->repository->removeVersionSlug($version_id, $slug_id);
	}

	public function getPages($ids)
	{
		return $this->repository->findByIds($ids);
	}

	public function deletePages($ids, $permanent = false)
	{
		return $this->repository->deletePages($ids, $permanent);
	}

	public function pageHistory($id, $limit = 10)
	{
		return $this->getHistory($id, $limit);
	}

	public function pageContributors($id)
	{
		return $this->history->users('pages', $id);
	}

	public function draftsForUser($page_id, $user_id = false)
	{
		if (!$user_id)
		{
			$user_id = $this->current_user_id;
		}

		return $this->repository->draftsForUser($page_id, $user_id);
	}

}
