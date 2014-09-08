<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

/**
 * Class HTML
 * @package CoandaCMS\Coanda\Core\Attributes\Types
 */
class HTML extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
	{
		return 'html';
	}

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.html';
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.html';
    }

    /**
     * @param $data
     * @param $is_required
     * @param $name
     * @return string
     * @throws \CoandaCMS\Coanda\Exceptions\AttributeValidationException
     */
    public function store($data, $is_required, $name, $parameters = [])
	{
		if ($data == '<p><br></p>')
		{
			$data = '';
		}

        if ($is_required && (!$data || $data == ''))
        {
            throw new AttributeValidationException($name . ' is required');
        }

        // Allowed tags - http://www.htmldog.com/reference/htmltags/
        $allowed_tags = [
            // Text
            'p','br',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'strong', 'em',
            'abbr', 'acronym', 'address', 'bdo', 'blockquote', 'cite', 'q', 'code',
            'ins', 'del',
            'dfn', 'kbd', 'pre',
            'samp','var',

            // Lists
            'ul', 'ol', 'li', 'dl', 'dt', 'dd',

            // Tables
            'table', 'tr', 'td', 'th',
            'tbody', 'thead', 'tfoot',
            'col', 'colgroup', 'caption',

            // Structure
            'div', 'span',

            // Images
            'img',

            // Links
            'a',

            // Object..
            'object', 'param',

            // iframes
            'iframe',
        ];

        $allowed_tag_string = '';

        foreach ($allowed_tags as $allowed_tag)
        {
            $allowed_tag_string .= '<' . $allowed_tag . '>';
        }

		return strip_tags($data, $allowed_tag_string);
	}

    /**
     * @param $data
     * @return mixed
     */
    public function data($data, $parameters = [])
	{
		return $data;
	}
}