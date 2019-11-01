<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeSsoLogMigration extends GeneratorCommand
{
    protected $name = "make:log_migration";
    protected $signature = 'make:log_migration';
    protected $description = 'Creates a database migration for an access log';
    protected $type = 'Migration';

    public function handle()
    {
        parent::handle();
    }

    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/2019_10_18_0000000_create_access_log_table.stub';
    }

    protected function getNameInput()
    {
        return '2019_10_18_0000000_create_access_log';
    }

    protected function getDefaultNamespace($rootNamespace)
    {

        return $rootNamespace.'\..\database\migrations';
    }
}
