<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeWebSSO extends GeneratorCommand
{
    protected $name = 'make:websso';
    protected $description = 'Create a new Northwestern WebSSO controller';
    protected $type = 'Controller';

    public function handle()
    {
        parent::handle();

        // GeneratorCommand only does one stub at a time, but I want both, so...
        Artisan::call(MakeDuo::class);
    }

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/WebSSOController.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers\Auth';
    }
}
