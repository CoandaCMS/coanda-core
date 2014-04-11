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

}
