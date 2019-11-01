<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;

class MakeWebSSO extends GeneratorCommand
{
    protected $signature = 'make:websso';
    protected $description = 'Create a new Northwestern WebSSO controller and sets up an access log  migration/model';
    protected $type = 'Controller';

    public function handle()
    {
        parent::handle();

        // GeneratorCommand only does one stub at a time, but I want both, so chaining these
        // is an expedient way of writing this cleanly!
        Artisan::call(MakeDuo::class);

        // Generate the migration unless they have it explicitly turned off in the .env, we default to DB logging + file logging.
        print("Add SSO_DB_LOG_ENABLED=false to your .env turn off database request logging \n");
        if (env('SSO_DB_LOG_ENABLED','true') != 'false') {
            Artisan::call(MakeSsoLogMigration::class);
            Artisan::call(MakeSsoLogModel::class);
        }
        $this->ejectRoutes();
    }

    protected function getNameInput()
    {
        return 'WebSSOController';
    }

    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/WebSSOController.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers\Auth';
    }

    protected function ejectRoutes()
    {
        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__.'/../../../stubs/routes.stub'),
            FILE_APPEND
        );
    }
}
