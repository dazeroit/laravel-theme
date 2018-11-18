<?php

namespace Dazeroit\Theme\Console\Commands;

use Dazeroit\Theme\Console\BeautyCLI;
use Dazeroit\Theme\Console\BeautyTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ThemeNew extends Command
{
    use BeautyCLI;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:new {theme? : The name of the folder theme to create. If omitted, the "promise" theme is used}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Theme skeleton';

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

        $this->runConsecutiveTasks([
            'Check theme' => function (BeautyTask $task) {
                $theme = $this->argument('theme') ?? config('theme.promise');
                if (!$theme) {
                    return $task->error("No theme declared !");
                }
                if (file_exists(theme_path($theme))) {
                    return $task->error("The theme '$theme' already exists !");
                }
                return $theme;
            },
            'Create theme skeleton' => function (BeautyTask $task, $theme) {
                File::makeDirectory(theme_folder($theme), 0777, true);
                $task->percentage(10);
                File::makeDirectory(theme_folder("$theme." . config('theme.views.folder')), 0777, true);
                $task->percentage(20);
                File::makeDirectory(theme_folder("$theme." . config('theme.layouts.folder')), 0777, true);
                $task->percentage(30);
                File::makeDirectory(theme_folder("$theme." . config('theme.partials.folder')), 0777, true);
                $task->percentage(40);
                File::makeDirectory(theme_folder("$theme." . config('theme.assets.path')), 0777, true);
                $task->percentage(50);
                $n = 50 / (count(config('theme.assets.folders')) ?? 1);
                foreach (config('theme.assets.folders') as $folder) {
                    File::makeDirectory(theme_folder("$theme." . config('theme.assets.path') . $folder), 0777, true);
                    $task->advance(null, $n);
                }
                File::put(theme_path("$theme/manifest.json"),"{ \n\t\"name\":\"$theme\"\n}");
                if (!config('theme.assets.npm.enable')) return false;

                return $theme;
            },
            'Install packages' => function (BeautyTask $task, $theme) {
                $this->output->newLine();
                $this->call('theme:npm',['theme'=>$theme],$this->getOutput());
            }
        ]);
    }
}
