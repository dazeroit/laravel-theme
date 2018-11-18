<?php

namespace Dazeroit\Theme\Assets;
use Dazeroit\Theme\Assets\Contracts\Collection as CollectionContract;

class Collection implements CollectionContract
{
    protected $name;
    protected $collection_js;
    protected $collection_css;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->collection_js = collect();
        $this->collection_css = collect();
    }

    public function add(string $path, string $name = null, $dependencies = null, string $version = null)
    {
        // TODO: Implement add() method.
    }

    public function addJs(string $path, string $name = null, $dependencies = null, string $version = null)
    {
        $name = $name ?? basename($path);
        $dependencies = is_array($dependencies) ? $dependencies : [$dependencies];
        $resource = new Resource($name,$path,$version,$dependencies);
    }

    public function addCss(string $path, string $name = null, $dependencies = null, string $version = null)
    {
        // TODO: Implement addCss() method.
    }

    public function remove(string $name_or_path)
    {
        // TODO: Implement remove() method.
    }

    public function removeJs(string $name_or_path)
    {
        // TODO: Implement removeJs() method.
    }

    public function removeCss(string $name_or_path)
    {
        // TODO: Implement removeCss() method.
    }

    public function render()
    {
        // TODO: Implement render() method.
    }

    public function renderJs()
    {
        // TODO: Implement renderJs() method.
    }

    public function renderCss()
    {
        // TODO: Implement renderCss() method.
    }

}