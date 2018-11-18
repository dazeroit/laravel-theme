<?php

if(!function_exists('theme_path')){
    /**
     * Returns the full theme path
     * @param string $path sub-path to includes
     * @return string
     */
    function theme_path(string $path = ''){
        return base_path(config('theme.path').$path);
    }
}

if(!function_exists('theme_folder')){
    /**
     * Returns the full theme directories structure
     * WARNING ! Don't use this function to generate file path, use theme_path function instead
     * @param string $folders
     * @return string
     */
    function theme_folder(string $folders = ''){
        return theme_path(str_replace('.','/',$folders));
    }
}

if(!function_exists('tview')){
    /**
     * Convenient shortcut function to generate Theme View
     * @param string|null $view
     * @param array $data
     * @return mixed
     */
    function tview(string $view = null,array $data = []){
        if($view === null)return \Dazeroit\Theme\Facades\Theme::current() ;
        return \Dazeroit\Theme\Facades\Theme::view($view,$data);
    }
}

if(!function_exists('theme_publish_path')){

    /**
     * Returns the full theme publish path
     * @param string $path
     * @return string
     */
    function theme_publish_path(string $path = ''){
        return base_path(config('theme.publish.public-folder').'/'.config('theme.publish.path').$path);
    }
}