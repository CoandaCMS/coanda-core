<?php namespace CoandaCMS\Coanda\Pages\PublishHandlers;

/**
 * Interface PublishHandlerInterface
 * @package CoandaCMS\Coanda\Pages\PublishHandlers
 */
interface PublishHandlerInterface {
	
	// public $name;
	// public $identifier;

    /**
     * @return mixed
     */
    public function validate();

    /**
     * @return mixed
     */
    public function execute($version, $urlRepository, $historyRepository);
}