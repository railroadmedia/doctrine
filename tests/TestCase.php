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
        $app->register(DoctrineServiceProvider::class);
    }
}