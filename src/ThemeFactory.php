<?php

namespace Dazeroit\Theme;

use Dazeroit\Theme\Contracts\ThemeFactory as ThemeFactoryContract;
use Dazeroit\Theme\Contracts\ThemeViewable;
use Dazeroit\Theme\Exceptions\ManifestNotFoundException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

abstract class ThemeFactory implements ThemeFactoryContract,ThemeViewable
{
    const CONTENT_VAR = "__CONTENT__";

    protected $theme;
    protected $fullPath;
    protected $namespace;
    protected $views;
    protected $layouts;
    protected $partials;
    protected $manifest;
    protected $shared = [];

    protected $last_view_instance ;
    protected $last_layout_instance ;
    protected $last_content;
    protected $last_call;

    protected function __construct(string $theme)
    {
        $this->content_var = "__CONTENT__";

        $this->theme = $theme;
        $this->fullPath = theme_path($theme);
        $this->namespace = config('theme.namespace');
        $this->views = config('theme.views');
        $this->layouts = config('theme.layouts');
        $this->partials = config('theme.partials');
    }

    public function manifest()
    {
        if($this->manifest === null) {
            if (!file_exists($this->fullPath . '/manifest.json')) {
                throw new ManifestNotFoundException("The manifest.json file of theme '{$this->theme}' was not found");
            }
            $this->manifest = json_decode(file_get_contents($this->fullPath . '/manifest.json'));
        }
        return $this->manifest;
    }

    public function info(string $property, $default = null)
    {
        return $this->manifest()->{$property} ?? $default ;
    }

    public function getTheme(): string
    {
       return $this->theme ;
    }

    public function view(string $view, array $data = [])
    {
        $this->last_view_instance = View::make($this->getViewNamespace($view),$data,$this->shared);
        $this->last_call = 'view' ;
        return $this ;
    }

    public function first(array $views, array $data = [])
    {
        foreach ($views as $i => $view){
            $views[$i] = $this->getViewNamespace($view);
        }
        $this->last_view_instance = View::first($views,$data,$this->shared);
        $this->last_call = 'view' ;
        return $this ;
    }

    protected function assignWithView($property,$value){
        if($this->last_view_instance !== null){
            $property = is_array($property) ? $property : [$property => $value] ;
            $this->last_view_instance->with($property);
        }
        return $this ;
    }
    protected function assignWithLayout($property,$value){
        if($this->last_layout_instance !== null){
            $property = is_array($property) ? $property : [$property => $value] ;
            $this->last_layout_instance->with($property);
        }
        return $this ;
    }
    public function with($property, $value = null)
    {
        switch ($this->last_call){
            case 'layout':
                return $this->assignWithLayout($property,$value);
            case 'view':
            default:
                return $this->assignWithView($property,$value);
        }
    }

    public function layout(string $layout, array $data = [])
    {
        $this->last_layout_instance = View::make($this->getLayoutNamespace($layout),$data,$this->shared);
        $this->last_call = 'layout' ;
        return $this ;
    }

    public function layoutFirst(array $layouts, array $data = [])
    {
        foreach ($layouts as $i => $layout){
            $layouts[$i] = $this->getLayoutNamespace($layout);
        }
        $this->last_layout_instance = View::first($layouts,$data,$this->shared);
        $this->last_call = 'layout' ;
        return $this ;
    }

    public function partial(string $partial, array $data = [])
    {
        return View::make($this->getPartialNamespace($partial),$data,$this->shared);
    }

    public function partialFirst(array $partials, array $data = [])
    {
        foreach ($partials as $i => $partial){
            $partials[$i] = $this->getPartialNamespace($partial);
        }
        return View::first($partials,$data,$this->shared);
    }

    public function composer($view, $callback)
    {
        View::composer($this->getViewNamespace($view),$callback);
        return $this;
    }

    public function composerLayout($layout, $callback)
    {
        View::composer($this->getLayoutNamespace($layout),$callback);
        return $this;
    }

    public function composerPartial($partial, $callback)
    {
        View::composer($this->getPartialNamespace($partial),$callback);
        return $this;
    }

    public function share($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];
        foreach ($keys as $key => $value) {
            $this->shared[$key] = $value;
        }
        return $this;
    }

    public function render()
    {
        if($this->last_layout_instance !== null){
            return $this->last_content = $this->last_layout_instance->with(self::CONTENT_VAR,$this->last_view_instance)->render();
        }elseif ($this->last_view_instance !== null){
            return $this->last_content = $this->last_view_instance->render();
        }

        return $this->last_content ;
    }

    public function compileString(string $raw)
    {
        return Blade::compileString($raw);
    }

    public function __toString()
    {
        return $this->render() ?? '';
    }

    public static function make(string $theme): ThemeFactoryContract
    {
        $factory = new class($theme) extends ThemeFactory{};
        return $factory ;
    }

    public function getViewNamespace(string $view):string
    {
        return "{$this->namespace}::{$this->theme}.{$this->views['folder']}.$view";
    }
    public function getLayoutNamespace(string $layout):string
    {
        return "{$this->namespace}::{$this->theme}.{$this->layouts['folder']}.$layout";
    }
    public function getPartialNamespace(string $partial):string
    {
        return "{$this->namespace}::{$this->theme}.{$this->partials['folder']}.$partial";
    }

}