<?php namespace CoandaCMS\Coanda\Pages\Repositories;

interface PageRepositoryInterface {

	public function find($id);

	public function findById($id);

	public function findByIds($ids);

	public function topLevel($per_page);

	public function create($type, $user_id, $parent_page_id);

	public function getDraftVersion($page_id, $version);

	public function getVersionByPreviewKey($preview_key);

	public function saveDraftVersion($version, $data);

	public function discardDraftVersion($version);

	public function draftsForUser($page_id, $user_id);

	public function publishVersion($version);

	public function createNewVersion($page_id, $user_id);

	public function history($page_id);

	public function deletePage($page_id, $permanent = false);

	public function deletePages($pages_ids, $permanent = false);

	public function trashed();

	public function trashedParentsForPage($page_id);

	public function restore($page_id, $restore_sub_pages);

	public function updateOrdering($new_orders);

}
