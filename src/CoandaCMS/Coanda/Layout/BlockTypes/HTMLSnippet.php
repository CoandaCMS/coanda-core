<?php namespace CoandaCMS\Coanda\Layout\BlockTypes;

use CoandaCMS\Coanda\Layout\BlockType;

class HTMLSnippet extends BlockType {

	public function identifier()
	{
		return 'htmlsnippet';
	}

	public function name()
	{
		return 'HMTL Snippet';
	}

	public function blueprint()
	{
		return [
				'html' => [
					'name' => 'HTML',
					'type' => 'html',
					'required' => true
				],
			];
	}
}