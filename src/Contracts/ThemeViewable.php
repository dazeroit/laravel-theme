<?php

namespace Dazeroit\Theme\Contracts;

interface ThemeViewable
{
    public function view(string $view,array $data = []);
    public function with($property,$value = null);
    public function layout(string $layout,array $data = []);
    public function render();
    public function compileString(string $raw);
    public function __toString();

}