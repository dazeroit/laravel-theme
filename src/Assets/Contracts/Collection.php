<?php

namespace Dazeroit\Theme\Assets\Contracts;


interface Collection
{
    public function add(string $path,string $name = null,$dependencies = null,string $version = null);
    public function addJs(string $path,string $name = null,$dependencies = null,string $version = null);
    public function addCss(string $path,string $name = null,$dependencies = null,string $version = null);
    public function remove(string $name);
    public function removeJs(string $name);
    public function removeCss(string $name);
    public function render();
    public function renderJs();
    public function renderCss();
}