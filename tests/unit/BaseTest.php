<?php

use Symfony\Component\Console\Output\BufferedOutput;

abstract class BaseTest extends \Orchestra\Testbench\TestCase {

	public function setUp()
	{
		parent::setUp();

        $artisan = $this->app->make('artisan');

        $output = new BufferedOutput;

        $artisan->call('migrate', [
            '--database' => 'testbench',
            '--path'     => 'migrations',
        ], $output);
	}

    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../../src';

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ));
    }

	protected function getPackageProviders()
    {
        return array('CoandaCMS\Coanda\CoandaServiceProvider');
    }

	protected function getPackageAliases()
    {
        return array(
            'Coanda' => 'CoandaCMS\Coanda\Facades\Coanda'
        );
    }
}