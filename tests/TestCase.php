<?php

namespace Railroad\Doctrine\Tests;

use Doctrine\ORM\EntityManager;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Railroad\Doctrine\Providers\DoctrineServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Generator
     */
    protected $faker;

    protected function setUp()
    {
        parent::setUp();

        $this->faker = Factory::create();

        // Run the schema update tool using our entity metadata
        $this->entityManager = app(EntityManager::class);

        $this->entityManager->getMetadataFactory()
            ->getCacheDriver()
            ->deleteAll();

        // make sure laravel is using the same connection
        DB::connection()
            ->setPdo(
                $this->entityManager->getConnection()
                    ->getWrappedConnection()
            );

        DB::connection()
            ->setReadPdo(
                $this->entityManager->getConnection()
                    ->getWrappedConnection()
            );

        Schema::create(
            'users',
            function (Blueprint $table) {
                $table->temporary();
                $table->increments('id');
                $table->timestamp('some_time');
                $table->date('some_date');
                $table->dateTime('some_date_time');
                $table->dateTimeTz('some_date_time_tz');
                $table->timestamps();
            }
        );

        Schema::create(
            'coupons',
            function (Blueprint $table) {
                $table->temporary();
                $table->increments('id');
                $table->timestamp('used_at_time')
                    ->nullable();
                $table->date('used_at_date')
                    ->nullable();
                $table->dateTime('used_at_datetime')
                    ->nullable();
                $table->dateTimeTz('used_at_datetimetz')
                    ->nullable();
                $table->timestamps();
            }
        );

        Schema::create(
            'resources',
            function (Blueprint $table) {
                $table->temporary();
                $table->increments('id');
                $table->text('download_url');
                $table->string('phone_number');
                $table->string('timezone');
                $table->string('gender');
            }
        );

        Schema::create(
            'addresses',
            function (Blueprint $table) {
                $table->temporary();
                $table->increments('id');
                $table->unsignedInteger('user_id');
            }
        );

        Schema::create(
            'contacts',
            function (Blueprint $table) {
                $table->temporary();
                $table->increments('id');
                $table->unsignedInteger('user_id');
            }
        );
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

        // doctrine db
        config()->set('doctrine.development_mode', $defaultConfig['development_mode'] ?? true);
        config()->set('doctrine.database_driver', 'pdo_sqlite');
        config()->set('doctrine.database_user', 'root');
        config()->set('doctrine.database_password', 'root');
        config()->set('doctrine.database_in_memory', true);

        // laravel db
        config()->set('database.default', config('usora.connection_mask_prefix') . 'sqlite');
        config()->set(
            'database.connections.' . config('usora.connection_mask_prefix') . 'sqlite',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );

        config()->set(
            'doctrine.entities',
            [
                [
                    'path' => __DIR__ . '/Fixtures',
                    'namespace' => 'Railroad\Doctrine\Tests\Fixtures',
                ],
            ]
        );

        $app->register(DoctrineServiceProvider::class);
    }
}