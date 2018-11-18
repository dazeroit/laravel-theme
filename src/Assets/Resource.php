<?php

namespace Dazeroit\Theme\Assets;


use Dazeroit\Theme\Facades\Theme;

class Resource
{
    protected $name;
    protected $path;
    protected $version;
    protected $dependencies;

    /**
     * Resource constructor.
     * @param string $name
     * @param string $path
     * @param string|null $version
     * @param array $dependencies
     */
    public function __construct(string $name, string $path, string $version = null, array $dependencies = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->version = $version;
        $this->setDependencies($dependencies);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function url()
    {
        return url($this->relativeUrl());
    }

    public function relativeUrl(){
        return config('theme.publish.path').Theme::current()->getTheme().'/'.config('theme.assets.path').$this->path.($this->version ? "?v={$this->version}" : '');
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function addDependency(Resource $resource)
    {
        $this->dependencies[] = $resource;
    }

    /**
     * @param array $dependencies
     */
    public function setDependencies(array $dependencies)
    {
        $this->dependencies = [];
        foreach ($dependencies as $dependency) {
            if ($dependency instanceof Resource) {
                $this->addDependency($dependency);
            }
        }
    }


}