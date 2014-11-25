<?php namespace CoandaCMS\Coanda\Pages\PageTypes;

class MissingPageType extends \CoandaCMS\Coanda\Pages\PageType {

    protected $name = 'Missing Page Type';
    protected $identifier = 'missing';
    protected $icon = 'fa-question-circle';

    protected $schema = [
        'name' => 'Name|textline|required|generates_slug'
    ];

}
