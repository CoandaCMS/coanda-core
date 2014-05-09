<?php namespace CoandaCMS\Coanda;

use Illuminate\Foundation\Application;
use CoandaCMS\Coanda\Coanda;

/**
 * Interface CoandaModuleProvider
 * @package CoandaCMS\Coanda
 */
interface CoandaModuleProvider {

    /**
     * @param Coanda $coanda
     * @return mixed
     */
    public function boot(Coanda $coanda);

    /**
     * @return mixed
     */
    public function adminRoutes();

    /**
     * @return mixed
     */
    public function userRoutes();

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return mixed
     */
    public function bindings(Application $app);

    /**
     * @param $permission
     * @param $parameters
     * @param $user_permissions
     * @return mixed
     */
    public function checkAccess($permission, $parameters, $user_permissions);

    public function buildAdminMenu($coanda);

}