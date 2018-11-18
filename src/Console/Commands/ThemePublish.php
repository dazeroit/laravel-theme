<?php

namespace Dazeroit\Theme\Console\Commands;

use Dazeroit\Theme\Console\BeautyCLI;
use Dazeroit\Theme\Console\BeautyTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ThemePublish extends Command
{
    use BeautyCLI;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:publish {theme : The theme to publish} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a theme, generally in public folder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->runTask('Publishing theme', function (BeautyTask $task, $theme) {
            if(!file_exists(theme_path($theme))){
                return $task->error("Theme '$theme' not found !");
            }
            if(!file_exists(theme_path("$theme/".config('theme.assets.path')))){
                return $task->error("Theme assets path for '$theme' not found !");
            }
            $source = theme_path("$theme/".config('theme.assets.path'));
            $destination = theme_publish_path("$theme/".config('theme.assets.path'));
            $max = count(File::allFiles($source));
            $this->copyFile($source,$destination,$task->getLoader(),$max,$this->option('force'));
            $task->message("Theme published");

        }, $this->argument('theme'));
    }

    protected function copyFile($source,$destination,$loader,$max,$force,$count = 0){
        $dir = opendir($source);
        @File::makeDirectory($destination, 0777, true);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($source . '/' . $file) ) {
                    $count = $this->copyFile($source . '/' . $file,$destination . '/' . $file,$loader,$max,$force,$count);
                }
                else {
                    if($force || !file_exists($destination . '/' . $file) || (filemtime($destination . '/' . $file) < filemtime($source . '/' . $file))){
                        File::copy($source . '/' . $file, $destination . '/' . $file);
                        $count++;
                    }
                }
            }
            $loader->advance(null,(100*$count)/($max != 0 ? $max : 1));
        }
        closedir($dir);
        return $count ;
    }
}
