<?php

namespace Dazeroit\Theme;
use Dazeroit\Theme\Contracts\Theme as ThemeContract;
use Dazeroit\Theme\Contracts\ThemeFactory as ThemeFactoryContract;
use Dazeroit\Theme\Contracts\ThemeViewable;
use Dazeroit\Theme\Exceptions\ThemeNotFoundException;
use Dazeroit\Theme\Exceptions\ThemeNotReadyException;

class Theme implements ThemeContract,ThemeViewable
{
    protected $themes;

    public function __construct()
    {
        $this->reset();
    }

    public function prepare(string $theme)
    {
        if(!$this->exists($theme)){
            throw new ThemeNotFoundException("The theme '$theme' was not found");
        }

        if(!$this->has($theme)){
            $this->themes->push([
                'theme' => $theme,
                'factory' => ThemeFactory::make($theme),
                'current' => false,
            ]);
        }
    }

    public function uses(string $theme):ThemeFactoryContract
    {
        $this->prepare($theme);
        $this->switch($theme);
        return $this->current();
    }

    public function current(): ThemeFactoryContract
    {
        return $this->themes->where('current',true)->first()['factory'];
    }

    public function switch(string $theme)
    {
        if(!$this->has($theme)){
            throw new ThemeNotReadyException("The theme '$theme' is not prepared");
        }
        $this->themes->transform(function($i) use($theme){
           if($i['theme'] === $theme){
                $i['current'] = true ;
           }else{
               $i['current'] = false ;
           }
           return $i ;
        });

    }

    public function exists(string $theme): bool
    {
        return file_exists(theme_path($theme));
    }

    public function has(string $theme): bool
    {
        return $this->themes->where('theme',$theme)->first() !== null ;
    }

    public function info(string $theme, string $property, $default = null)
    {

        if(!$this->has($theme)){
            throw new ThemeNotReadyException("The theme '$theme' is not prepared");
        }

        return $this->themes->where('theme',$theme)->first()['factory']->manifest()->{$property} ?? $default ;
    }

    public function remove(string $theme)
    {
        $key = false ;
        $this->themes->search(function($i,$k) use($theme,&$key){
            if($i['theme'] == $theme){
                $key = $k ;
            }
        });
        if($key !== false){
            $this->themes->forget($key);
        }
    }

    public function reset()
    {
        $this->themes = collect();
    }

    public function view(string $view, array $data = [])
    {
        return $this->current()->view($view,$data);
    }

    public function with($property, $value = null)
    {
        return $this->current()->with($property,$value);
    }

    public function layout(string $layout, array $data = [])
    {
        return $this->current()->layout($layout,$data);
    }

    public function render()
    {
        return $this->current()->render();
    }

    public function compileString(string $raw)
    {
        // TODO: Implement compileString() method.
    }

    public function __toString()
    {
        return $this->render() ?? '' ;
    }
}