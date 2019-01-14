<?php

namespace Railroad\Doctrine\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Railroad\Doctrine\Providers\DoctrineServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = config()->get('doctrine', []);

        config()->set('doctrine', array_merge(require __DIR__ . '/../config/doctrine.php', $config));

        $app->register(DoctrineServiceProvider::class);
    }
}