<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeSsoLogModel extends GeneratorCommand
{
    protected $name = "make:log_model";
    protected $signature = 'make:log_model';
    protected $description = 'Creates a model for the access log table';
    protected $type = 'Model';

    public function handle()
    {
        parent::handle();
    }

    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/Access.stub';
    }

    protected function getNameInput()
    {
        return 'Access';
    }

    protected function getDefaultNamespace($rootNamespace)
    {

        return $rootNamespace;
    }
}
