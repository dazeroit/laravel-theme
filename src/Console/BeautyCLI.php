<?php

namespace Dazeroit\Theme\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeautyLoader
{

    protected $loader;

    public function __construct(OutputInterface $output, int $max = 100, $message = null)
    {
        $this->loader = new ProgressBar($output, $max);
        $this->loader->setFormat(' [%bar%] %percent:3s%% - %message% ');
        $this->loader->setBarCharacter('<comment>■</comment>');
        $this->loader->setEmptyBarCharacter(' ');
        $this->loader->setProgressCharacter('■');
        $this->message($message ?? '');
    }

    public function start($message = null)
    {
        if ($message) $this->message($message);
        $this->loader->start();
    }

    public function stop()
    {
        $this->loader->finish();
    }

    public function message($message, $placeholder = 'message')
    {
        $this->loader->setMessage($message, $placeholder);
    }

    public function advance($message = null, int $steps = 1)
    {
        $this->loader->advance($steps);
        if ($message) $this->message($message);
    }

    public function setProgress(int $step)
    {
        $this->loader->setProgress($step);
    }

    public function percentage(int $percentage, $message = null)
    {
        $this->loader->setProgress(($this->loader->getMaxSteps() * $percentage) / 100);
        if ($message) $this->message($message);
    }

    public function wait(int $milliseconds)
    {
        usleep($milliseconds * 1000);
    }
}

class BeautyTask
{

    protected $name;
    protected $task;
    protected $steps;
    protected $loader;
    protected $output;
    protected $running;
    protected $consumed;
    protected $start_time;
    protected $end_time;
    protected $elapsed_time;
    protected $warned = false ;

    public function __construct(OutputInterface $output, string $name, callable $task, int $steps = 100)
    {
        $this->output = $output;
        $this->name = $name;
        $this->task = $task;
        $this->steps = $steps;
        $this->loader = new BeautyLoader($this->output, $this->steps, $this->name);
    }

    public function run(array $args = [])
    {
        if (!$this->running) {
            $this->running = true;
            array_unshift($args, $this);
            $this->loader->start($this->name);
            $this->start_time = microtime(true);
            $response = call_user_func_array($this->task, $args);
            $this->stop();
            return $response;
        }
    }

    public function stop($with_error = false)
    {
        if ($this->running && !$this->consumed) {
            $this->end_time = microtime(true);
            $this->elapsed_time = $this->end_time - $this->start_time;
            if (!$with_error) {
                $this->loader->stop();
                if(!$this->warned){
                    $this->output->writeln('<info>✔</info>');
                }else{
                    $this->output->writeln('<comment>⚠</comment>');
                }
            }else{
                $this->output->writeln('<error>✖</error>');
            }
            $this->consumed = true;
        }
    }

    public function progress(int $step = 1, $message = null)
    {
        if ($this->consumed) return;

        $this->loader->setProgress($step);
        if ($message) $this->loader->message($message);
    }

    public function percentage(int $percentage)
    {
        if ($this->running) {
            $this->loader->percentage($percentage);
        }
    }

    public function advance($message = null,int $steps = 1)
    {
        if ($this->running) {
            $this->loader->advance($message,$steps);
        }
    }

    public function getElapsedTime()
    {
        return $this->elapsed_time;
    }

    public function getLoader(){
        return $this->loader ;
    }

    public function warning($message){
        if ($this->running) {
            $this->warned = true ;
            $this->output->warning($message);
        }
    }
    public function error($message)
    {
        $this->stop(true);
        $this->output->writeln('<error>' . $message . '</error>');
        return false;
    }

    public function message($message, $placeholder = 'message')
    {
        if ($this->running) {
            $this->loader->message($message, $placeholder);
        }
    }

    public function wait(int $milliseconds){
        $this->loader->wait($milliseconds);
    }
}

trait BeautyCLI
{
    protected $loader;
    protected $loader_max_steps = 100;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loader = new BeautyLoader($output, $this->loader_max_steps);
        parent::execute($input, $output);
    }

    protected function createTask(string $name, callable $task)
    {
        return new BeautyTask($this->getOutput(), $name, $task, $this->loader_max_steps);
    }

    protected function runTask(string $name,callable $task,$args = null){
        return $this->createTask($name,$task)->run(is_array($args) ? $args : [$args]);
    }
    protected function runConsecutiveTasks(array $tasks, $args = null ,$ignore_errors = false)
    {
        $response = $args;
        foreach ($tasks as $name => $task) {
            $response = $this->createTask($name, $task)->run(is_array($response) ? $response : [$response]);
            if ($response === false && $ignore_errors) continue;
            if ($response === false && !$ignore_errors) break;
        }
    }

}