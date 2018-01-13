<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeCheckQueue extends GeneratorCommand
{
    protected $name = 'make:command:checkQueue';
    protected $description = 'Create a new command to consume an ESB MQ topic';
    protected $type = 'Command';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/queue-check.stub';
    } // end getStub

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Console\Commands';
    } // end getDefaultNamespace

} // MakeCheckQueue
