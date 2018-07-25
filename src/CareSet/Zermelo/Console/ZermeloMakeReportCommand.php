<?php

namespace CareSet\Zermelo\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class ZermeloMakeReportCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:zermelo {report_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';


    /**
     * Use the stub in stubs directory to write a php file to the configured namespace
     * directory.
     *
     * @return void
     */
    public function handle()
    {
        $content = file_get_contents( __DIR__.'/stubs/ZermeloReport.stub' );

        $namespace = config( 'zermelo.REPORT_NAMESPACE', 'Zermelo' );
        $content = str_replace( "{{ report_namespace }}",$namespace , $content );

        $report_name = $this->argument( 'report_name' );
        $content = str_replace( "{{ report_name }}",$report_name , $content );

        $fs = new Filesystem();
        $app_path = str_replace( '\\', '/', $namespace );
        $app_path = str_replace( "App/", "", $app_path );
        $path = app_path().'/'.$app_path.'/'.$report_name.'.php';
        $fs->put($path, $content );
    }
}
