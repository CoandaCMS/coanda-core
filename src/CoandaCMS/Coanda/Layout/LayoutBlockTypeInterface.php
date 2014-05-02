<?php namespace CoandaCMS\Coanda\Layout;

interface LayoutBlockTypeInterface {
	
    public function name();

    public function identifier();

    public function template();

    public function attributes();

    public function generateName($block);

}