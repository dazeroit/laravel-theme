<?php

namespace Dazeroit\Theme\Contracts;

interface ThemeViewable
{
    public function view(string $view,array $data = []);
    public function first(array $views,array $data = []);
    public function with($property,$value = null);
    public function layout(string $layout,array $data = []);
    public function layoutFirst(array $layouts, array $data = []);
    public function partial(string $partial,array $data = []);
    public function partialFirst(array $partials,array $data = []);
    public function composer($view,$callback);
    public function composerLayout($layout,$callback);
    public function composerPartial($partial,$callback);
    public function creator($view,$callback);
    public function creatorLayout($layout,$callback);
    public function creatorPartial($partial,$callback);
    public function viewExists($view):bool;
    public function layoutExists($layout):bool;
    public function partialExists($partial):bool;
    public function render();
    public function share($key,$value = null);
    public function compileString(string $raw);
    public function __toString();

}