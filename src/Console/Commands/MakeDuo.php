<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeDuo extends GeneratorCommand
{
    protected $signature = 'make:websso:duo';
    protected $description = 'Create Duo controller for webSSO';
    protected $type = 'Controller';

    // Will be invoked by make:websso, don't advertise this command!
    protected $hidden = true;

    public function handle()
    {
        if (parent::handle() === false) {
            return;
        }

        $this->writeDuoTemplate();
    }

    protected function getNameInput()
    {
        return 'DuoController';
    }

    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/DuoController.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers\Auth';
    }

    protected function writeDuoTemplate()
    {
        $path = resource_path('views/auth/mfa.blade.php');
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }

        $this->files->put($path, file_get_contents(__DIR__.'/../../../stubs/mfa.blade.stub'));
    }
}
