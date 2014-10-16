<?php namespace CoandaCMS\Coanda\Media\ImageHandlers;

use CoandaCMS\Coanda\Media\Exceptions\ImageGenerationException;

use Intervention\Image\Exception\InvalidImageTypeException;
use Intervention\Image\Image as ImageFactory;

class DefaultImageHandler {

    /**
     * @param $original
     * @param $output
     * @return ImageFactory
     * @throws ImageGenerationException
     */
    private function initaliseImageFactory($original, $output)
    {
        if (file_exists($original))
        {
            $path_parts = pathinfo($output);

            if (!is_dir($path_parts['dirname']))
            {
                mkdir($path_parts['dirname'], 0777, true);
            }

            $imageFactory = ImageFactory::make($original);

            return $imageFactory;
        }

        throw new ImageGenerationException;
    }

    /**
     * @param $original
     * @param $output
     * @param $size
     * @return mixed
     * @throws ImageGenerationException
     */
    public function crop($original, $output, $size)
	{
        try
        {
            $imageFactory = $this->initaliseImageFactory($original, $output);
            $imageFactory->grab($size, $size)->save($output, $this->quality());

            return $output;
        }
        catch (InvalidImageTypeException $exception)
        {
            throw new ImageGenerationException;
        }
	}

    /**
     * @param $original
     * @param $output
     * @param $size
     * @throws ImageGenerationException
     */
    public function resize($original, $output, $size)
	{
        try
        {
            $maintain_ratio = true;
            $upscale = false;

            $imageFactory = $this->initaliseImageFactory($original, $output);
            $imageFactory->resize($size, null, $maintain_ratio, $upscale)->save($output, $this->quality());
        }
        catch (InvalidImageTypeException $exception)
        {
            throw new ImageGenerationException;
        }
	}

    /**
     * @return mixed
     */
    private function quality()
    {
        return \Config::get('coanda::coanda.image_quality');
    }
}