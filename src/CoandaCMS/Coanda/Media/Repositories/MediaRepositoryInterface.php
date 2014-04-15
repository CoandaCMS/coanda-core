<?php namespace CoandaCMS\Coanda\Media\Repositories;

interface MediaRepositoryInterface {

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id);

    /**
     * @param $ids
     * @return mixed
     */
    public function findByIds($ids);

    public function handleUpload($file);

    public function removeById($media_id);

    public function tags($per_page);

    public function tagMedia($media_id, $tag_name);

    public function removeTag($media_id, $tag_id);
    
    public function getTags($media_id);

    public function recentTagList($limit);

    public function forTag($tag_id, $per_page);

    public function maxFileSize();

}
