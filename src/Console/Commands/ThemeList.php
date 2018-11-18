<?php

namespace Dazeroit\Theme\Console\Commands;

use Dazeroit\Theme\Console\BeautyCLI;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ThemeList extends Command
{
    use BeautyCLI;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show a themes list';

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
        $themes = File::directories(theme_path());
        foreach ($themes as $theme){
            $this->info("► ".basename($theme));
        }
    }
}
