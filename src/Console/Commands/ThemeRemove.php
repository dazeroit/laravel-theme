<?php

namespace Dazeroit\Theme\Console\Commands;

use Dazeroit\Theme\Console\BeautyCLI;
use Dazeroit\Theme\Console\BeautyTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ThemeRemove extends Command
{
    use BeautyCLI;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:remove {theme : The theme to remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a theme';

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
        $this->runTask('Removing theme',function(BeautyTask $task,$theme){
            if(!file_exists(theme_path($theme))){
                return $task->error("Theme '$theme' not found");
            }

            if($this->confirm("Are you sure to remove theme '$theme' ?")){
                if(!File::deleteDirectory(theme_path($theme))){
                    return $task->error("Something gone wrong...");
                }
                $task->message("Theme successfully removed");
            }else{
                $task->message("Aborted");
            }

        },$this->argument('theme'));
    }
}
