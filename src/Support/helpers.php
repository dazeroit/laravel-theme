<?php

if(!function_exists('theme_path')){
    function theme_path(string $path = ''){
        return base_path(config('theme.path').$path);
    }
}

if(!function_exists('tview')){
    function tview(string $view = null,array $data = []){
        if($view === null)return \Dazeroit\Theme\Facades\Theme::current() ;
        return \Dazeroit\Theme\Facades\Theme::view($view,$data);
    }
}