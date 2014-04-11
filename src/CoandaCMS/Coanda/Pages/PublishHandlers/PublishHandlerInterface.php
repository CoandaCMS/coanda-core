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
    public function validate($version, $data);

    /**
     * @return mixed
     */
    public function execute($version, $data, $urlRepository, $historyRepository);
}