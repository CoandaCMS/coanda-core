<?php namespace CoandaCMS\Coanda\Pages\PageTypes;

class MissingPageType extends \CoandaCMS\Coanda\Pages\PageType {

    /**
     * @var string
     */
    protected $name = 'Missing Page Type';
    /**
     * @var string
     */
    protected $identifier = 'missing';
    /**
     * @var string
     */
    protected $icon = 'fa-question-circle';

    /**
     * @var array
     */
    protected $schema = [
        'name' => 'Name|textline|required|generates_slug'
    ];

    /**
     * @param $version
     * @param array $data
     * @return string
     */
    public function template($version, $data = [])
    {
        return 'coanda::missing_page_type';
    }
}
