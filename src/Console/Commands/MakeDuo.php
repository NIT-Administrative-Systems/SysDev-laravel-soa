<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeDuo extends GeneratorCommand
{
    protected $name = 'make:websso:duo';
    protected $description = 'Create Duo controller for webSSO';
    protected $type = 'Controller';

    // Will be invoked by make:websso, don't advertise this command!
    protected $hidden = true;

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/DuoController.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers\Auth';
    }
}
