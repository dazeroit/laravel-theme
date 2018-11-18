<?php

namespace Dazeroit\Theme\Console\Commands;

use Dazeroit\Theme\Console\BeautyCLI;
use Dazeroit\Theme\Console\BeautyTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ThemeClone extends Command
{
    use BeautyCLI;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:clone {theme-source : The theme to clone} {theme-destination : The destination theme name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone a theme';

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
        $this->runTask('Cloning theme',function(BeautyTask $task,$source,$destination){
            if(!file_exists(theme_path($source))){
                return $task->error("Theme '$source' not found !");
            }
            if(file_exists(theme_path($destination))){
                return $task->error("Theme '$destination' already exists !" );
            }
            if(!File::copyDirectory(theme_path($source),theme_path($destination))){
                return $task->error("Something gone wrong...");
            }
            $task->message('Theme cloned successfully');

        },[$this->argument('theme-source'),$this->argument('theme-destination')]);
    }
}
