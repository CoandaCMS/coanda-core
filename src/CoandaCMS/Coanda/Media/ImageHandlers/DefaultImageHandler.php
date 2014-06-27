<?php namespace CoandaCMS\Coanda\Media\ImageHandlers;

use CoandaCMS\Coanda\Media\Exceptions\ImageGenerationException;

use Intervention\Image\Image as ImageFactory;

class DefaultImageHandler {

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

	public static function crop($original, $output, $size)
	{
        $imageFactory = $this->initaliseImageFactory($original, $output);
        $imageFactory->grab($size, $size)->save($output);

        return $output;
	}

	public static function resize($original, $output, $size)
	{
		$maintain_ratio = true;
		$upscale = false;

        $imageFactory = $this->initaliseImageFactory($original, $output);
        $imageFactory->resize($size, $size, $maintain_ratio, $upscale)->save($output);
	}

}