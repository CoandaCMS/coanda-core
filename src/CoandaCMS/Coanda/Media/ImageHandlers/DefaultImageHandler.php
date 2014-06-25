<?php namespace CoandaCMS\Coanda\Media\ImageHandlers;

use CoandaCMS\Coanda\Media\Exceptions\ImageGenerationException;

use Intervention\Image\Image as ImageFactory;

class DefaultImageHandler {

	public static function crop($original, $output, $size)
	{
        if (file_exists($original))
        {
        	$path_parts = pathinfo($output);

			if (!is_dir($path_parts['dirname']))
            {
				mkdir($path_parts['dirname'], 0777, true);
            }

            $imageFactory = ImageFactory::make($original);
            $imageFactory->grab($size, $size)->save($output);

            return $output;
        }

        throw new ImageGenerationException;
	}

	public static function resize($original, $output, $size)
	{
		$maintain_ratio = true;
		$upscale = false;

        if (file_exists($original))
        {
        	$path_parts = pathinfo($output);

			if (!is_dir($path_parts['dirname']))
            {
				mkdir($path_parts['dirname'], 0777, true);
            }

            $imageFactory = ImageFactory::make($original);
            $imageFactory->resize($size, $size, $maintain_ratio, $upscale)->save($output);

            return $output;
        }

        throw new ImageGenerationException;
	}

}