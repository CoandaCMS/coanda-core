<?php namespace CoandaCMS\Coanda\Pages\PageTypes;

class MissingPageType extends \CoandaCMS\Coanda\Pages\PageType {

    public function identifier()
    {
        return 'missing';
    }

    public function name()
    {
        return 'Missing Page Type';
    }

    public function icon()
    {
        return 'fa-question-circle';
    }

    public function attributes()
    {
        return [
            'question' => [
                'name' => 'Name',
                'type' => 'textline',
                'required' => true,
                'generates_slug' => true
                ]
            ];
    }

    public function generateName($version)
    {
        return '';
    }
}
