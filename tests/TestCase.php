<?php

namespace W360\ImportGpgExcel\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use W360\ImportGpgExcel\ImportGpgExcelServiceProvider;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

abstract class TestCase extends BaseTestCase
{

    /**
     * Setup the test environment.
     *
     * @return void
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(EloquentFactory::class)->load(__DIR__ . "/../database/factories");
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     *
     * @throws \Mockery\Exception\InvalidCountException
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            ImportGpgExcelServiceProvider::class,
            \Maatwebsite\Excel\ExcelServiceProvider::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {

        // import the CreateUsersTable class from the migration
        include_once __DIR__ . '/../database/migrations/create_users_table.php.stub';
        // run the up() method of that migration class
        (new \CreateUsersTable)->up();

        // import the CreateFailedJobsTable class from the migration
        include_once __DIR__ . '/../database/migrations/create_failed_jobs_table.php.stub';
        // run the up() method of that migration class
        (new \CreateFailedJobsTable)->up();

        // import the CreateJobsTable class from the migration
        include_once __DIR__ . '/../database/migrations/create_jobs_table.php.stub';
        // run the up() method of that migration class
        (new \CreateJobsTable)->up();

        // import the CreateImportsTable class from the migration
        include_once __DIR__ . '/../database/migrations/create_imports_table.php.stub';
        // run the up() method of that migration class
        (new \CreateImportsTable)->up();

        $app->useStoragePath(__DIR__ . '/../storage/');

    }

}