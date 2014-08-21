<?php namespace CoandaCMS\Coanda\Urls;

/**
 * Class Slugifier
 * @package CoandaCMS\Coanda\Urls
 */
class Slugifier {

    /**
     * @param $slug
     * @return bool
     */
    public function validate($slug)
	{
		if ($slug == '')
		{
			return false;
		}

		if ( preg_match('/^[\/\-_.a-z0-9]*$/i', $slug))
		{
			return true;
		}

		return false;
	}

    /**
     * @param $text
     * @return mixed|string
     */
    public static function convert($text)
	{
		$text = str_replace("'", '', $text);
		
		// Reference: http://stackoverflow.com/questions/3371697/replacing-accented-characters-php
		$replace_list = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y');

		$text = strtr($text, $replace_list);

		$text = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $text);
		$text = strtolower(trim($text, '-'));

	    return $text;
	}

}