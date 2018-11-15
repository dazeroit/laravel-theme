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
    protected $promise;

    public function __construct()
    {
        $this->reset();
        $this->promise(config('theme.promise','default'));
    }

    public function prepare($theme)
    {
        $themes = is_array($theme) ? $theme : [$theme];
        foreach ($themes as $theme) {
            if (!$this->exists($theme)) {
                throw new ThemeNotFoundException("The theme '$theme' was not found");
            }

            if (!$this->has($theme)) {
                $this->themes->push([
                    'theme' => $theme,
                    'factory' => ThemeFactory::make($theme),
                    'current' => false,
                ]);
            }
        }
        return $this;
    }

    public function prepareAll()
    {
        $to_prepare = [] ;
        $list = scandir(theme_path());
        foreach ($list as $dir){
            if($dir != '.' && $dir != '..' && is_dir(theme_path($dir))){
                $to_prepare[] = $dir ;
            }
        }
        return $this->prepare($to_prepare);
    }

    public function promise($theme){
        $this->promise = ThemeFactory::make($theme);
        return $this;
    }

    public function uses(string $theme):ThemeFactoryContract
    {
        $this->prepare($theme);
        $this->switch($theme);
        return $this->current();
    }

    public function current(): ThemeFactoryContract
    {
        return $this->themes->where('current',true)->first()['factory'] ?? $this->promise;
    }

    public function link(string $theme): ThemeFactoryContract
    {
        if(!$this->has($theme)){
            throw new ThemeNotReadyException("The theme '$theme' is not prepared");
        }

        return $this->themes->where('theme',$theme)->first()['factory'] ;
    }

    public function switch(string $theme)
    {
        if(!$this->has($theme)){
            throw new ThemeNotReadyException("The theme '$theme' is not prepared");
        }
        $this->themes->transform(function($i) use($theme){
            $i['current'] = $i['theme'] === $theme ? true : false ;
            return $i ;
        });

        return $this;
    }

    public function exists(string $theme): bool
    {
        return file_exists(theme_path($theme));
    }

    public function has(string $theme): bool
    {
        return $this->themes->where('theme',$theme)->first() !== null ;
    }

    public function info(string $property, $default = null)
    {
        return $this->current()->info($property,$default);
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

        return $this;
    }

    public function reset()
    {
        $this->themes = collect();
        return $this;
    }

    public function view(string $view, array $data = [])
    {
        return $this->current()->view($view,$data);
    }

    public function first(array $views, array $data = [])
    {
        return $this->current()->first($views,$data);
    }

    public function with($property, $value = null)
    {
        return $this->current()->with($property,$value);
    }

    public function layout(string $layout, array $data = [])
    {
        return $this->current()->layout($layout,$data);
    }

    public function layoutFirst(array $layouts, array $data = [])
    {
        return $this->current()->layoutFirst($layouts,$data);
    }

    public function partial(string $partial, array $data = [])
    {
        return $this->current()->partial($partial,$data);
    }

    public function partialFirst(array $partials, array $data = [])
    {
        return $this->current()->partialFirst($partials,$data);
    }

    public function composer($view, $callback)
    {
        return $this->current()->composer($view,$callback);
    }

    public function composerLayout($layout, $callback)
    {
        return $this->current()->composerLayout($layout,$callback);
    }

    public function composerPartial($partial, $callback)
    {
       return $this->current()->composerPartial($partial,$callback);
    }

    public function render()
    {
        return $this->current()->render();
    }

    public function share($key, $value = null)
    {
        return $this->current()->share($key,$value);
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