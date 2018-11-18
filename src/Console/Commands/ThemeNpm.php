<?php

namespace Dazeroit\Theme\Console\Commands;

use Dazeroit\Theme\Console\BeautyCLI;
use Dazeroit\Theme\Console\BeautyTask;
use Illuminate\Console\Command;

class ThemeNpm extends Command
{
    use BeautyCLI;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:npm {theme : The folder theme} {--only-global : Install only global packages} {--only-package : Install only package.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install theme packages';

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

        $this->runTask('npm : install', function (BeautyTask $task, $theme) {

            if (empty(config('theme.assets.npm.install')) && !file_exists(theme_path("$theme/" . config('theme.assets.path') . "package.json"))) {
                $task->message('npm : nothing to install...');
                return $task->stop();
            }

            // Global installation
            if (!$this->option('only-package') && !empty(config('theme.assets.npm.install'))) {
                $n = count(config('theme.assets.npm.install'));
                $flags = '';
                foreach (config('theme.assets.npm.flags') as $flag) {
                    $flags .= " $flag";
                }
                $assets_dir = theme_path("$theme/" . config('theme.assets.path'));
                chdir($assets_dir);

                if (!file_exists($assets_dir . "package.json")) {
                    exec("npm init -y", $out, $exit_code);
                    if ($exit_code != 0) {
                        $task->message('npm : bad package.json :(');
                        return $task->stop(true);
                    } else {
                        $task->message('npm : package.json created');
                    }
                };

                $warning = false;
                foreach (config('theme.assets.npm.install') as $package) {

                    $task->message("npm : install $package");
                    exec("npm install $flags $package", $out, $exit_code);

                    if ($exit_code != 0) $warning = true;
                    $task->advance();
                }
                if($warning){
                    $task->warning('npm : something gone wrong...');
                }
            }

            // Install from package.json
            if (!$this->option('only-global') && file_exists(theme_path("$theme/" . config('theme.assets.path') . "package.json"))) {
                $assets_dir = theme_path("$theme/" . config('theme.assets.path'));
                chdir($assets_dir);
                $task->message("npm : installing package.json dependencies");
                exec("npm install", $out, $exit_code);
                if ($exit_code != 0) {
                    $task->warning('npm : something gone wrong...');
                }
                $task->advance();
            }

            $task->message('npm : installation complete');

        }, $this->argument('theme'));

    }
}
