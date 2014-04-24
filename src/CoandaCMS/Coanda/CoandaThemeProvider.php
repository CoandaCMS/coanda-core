<?php namespace CoandaCMS\Coanda;

/**
 * Interface CoandaModuleProvider
 * @package CoandaCMS\Coanda
 */
interface CoandaThemeProvider {

    /**
     * @param Coanda $coanda
     * @return mixed
     */
	public function boot($coanda);

	public function render($what, $with = []);

}