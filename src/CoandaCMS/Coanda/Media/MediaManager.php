<?php namespace CoandaCMS\Coanda\Media;

use CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface;

class MediaManager {

    /**
     * @var MediaRepositoryInterface
     */
    private $repository;
    /**
     * @param MediaRepositoryInterface $mediaRepository
     */
    public function __construct(MediaRepositoryInterface $mediaRepository)
    {
        $this->repository = $mediaRepository;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getMediaById($id)
    {
        try
        {
            return $this->repository->findById($id);
        }
        catch (\CoandaCMS\Coanda\Media\Exceptions\MediaNotFound $exception)
        {
            return false;
        }
    }

    /**
     * @param $media_id
     * @param $filename
     * @return mixed
     */
    public function generateResizedImage($media_id, $filename)
    {
        $media = $this->getMediaById($media_id);

        // We can only do this for images...
        if ($media && $media->type == 'image')
        {
            return $media->generateImage($filename);
        }
    }
}
