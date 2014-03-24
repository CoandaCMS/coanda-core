<?php namespace CoandaCMS\Coanda\Urls;

class Slugifier {
	
	public function validate($slug)
	{
		if ($slug == '')
		{
			return false;
		}
		
		if ( preg_match('/^[\/\-_a-z0-9]*$/', $slug))
		{
			return true;
		}

		return false;
	}

	public static function convert($text)
	{
		// Reference: http://code.seebz.net/p/to-permalink/
		if($text !== mb_convert_encoding( mb_convert_encoding($text, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
		{
			$text = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text));	
		}

		$text = htmlentities($text, ENT_NOQUOTES, 'UTF-8');
		$text = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\\1', $text);
		$text = html_entity_decode($text, ENT_NOQUOTES, 'UTF-8');
		$text = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $text);
		$text = strtolower(trim($text, '-'));

	    return $text;
	}

}