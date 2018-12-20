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

    public function library(string $path, string $name = null, $dependencies = null, string $version = null)
    {
        return $this->addOrLibrary(true,$path,$name,$dependencies,$version);
    }
    public function libraryJs(string $path, string $name = null, $dependencies = null, string $version = null)
    {
        return $this->addJsOrLibrary(true,$path,$name,$dependencies,$version);
    }
    public function libraryCss(string $path, string $name = null, $dependencies = null, string $version = null)
    {
        return $this->addCssOrLibrary(true,$path,$name,$dependencies,$version);
    }

    public function add(string $path, string $name = null, $dependencies = null, string $version = null)
    {
        return $this->addOrLibrary(false,$path,$name,$dependencies,$version);
    }
    public function addJs(string $path, string $name = null, $dependencies = null, string $version = null)
    {
        return $this->addJsOrLibrary(false,$path,$name,$dependencies,$version);
    }
    public function addCss(string $path, string $name = null, $dependencies = null, string $version = null)
    {
        return $this->addCssOrLibrary(false,$path,$name,$dependencies,$version);
    }

    public function link(string $name)
    {
        if(($key = $this->getCollectionCssKeyByName($name)) !== false){
            $r = $this->collection_css->get($key);
            $r['linked'] = true ;
            $this->collection_css->put($key,$r);
        }else if(($key = $this->getCollectionJsKeyByName($name)) !== false){
            $r = $this->collection_js->get($key);
            $r['linked'] = true ;
            $this->collection_js->put($key,$r);
        }else{
            throw new \Exception(sprintf('Missed dependency link "%s"',$name));
        }
    }
    protected function addOrLibrary(bool $isLibrary,string $path, string $name = null, $dependencies = null, string $version = null)
    {
        $finfo = new \SplFileInfo($path);
        $ext = strtolower($finfo->getExtension());
        switch ($ext) {
            case 'js':
                return $this->addJsOrLibrary($isLibrary,$path, $name, $dependencies, $version);
            case 'css':
                return $this->addCssOrLibrary($isLibrary,$path, $name, $dependencies, $version);
            default :
                return $this;
        }
    }

    protected function addJsOrLibrary(bool $isLibrary,string $path, string $name = null, $dependencies = null, string $version = null)
    {
        $this->collection_js->push($this->createResourcePack('js',$isLibrary,$path,$name,$dependencies,$version));
        return $this;
    }

    protected function addCssOrLibrary(bool $isLibrary,string $path, string $name = null, $dependencies = null, string $version = null)
    {
        $this->collection_css->push($this->createResourcePack('css',$isLibrary,$path,$name,$dependencies,$version));
        return $this;
    }

    protected function createResourcePack(string $type,bool $isLibrary,string $path, string $name = null, $dependencies = null, string $version = null):array{
        $resource = $this->createResource($type,$path,$name,$dependencies,$version);
        return [
            'name'          => $resource->getName(),
            'resource'      => $resource,
            'type'          => $type,
            'library'       => $isLibrary,
            'linked'        => false,
            'rendered'      => false,
        ];
    }

    protected function createResource(string $type,string $path, string $name = null, $dependencies = null, string $version = null):Resource
    {
        $name = $name ?? basename($path);
        $dependencies = is_array($dependencies) ? $dependencies : [$dependencies];
        foreach ($dependencies as &$dependency){
            if(!($dependency instanceof Resource)){
                if(!$dependency)continue;
                if(($key = $this->{'getCollection'.ucfirst($type).'KeyByName'}($dependency)) !== false){
                    $dependency = $this->{'collection_'.$type}->get($key)['resource'];
                }else {
                    throw new \Exception(sprintf('Missed dependency "%s"',$dependency));
                }
            }
        }
        return (new Resource($name, $path, $version, $dependencies));
    }

    public function getCollectionJsKeyByName(string $name)
    {
        $key = $this->collection_js->search(function($item,$key)use($name){
            return ($item['name'] == $name);
        });
        return $key ;
    }
    public function getCollectionCssKeyByName(string $name)
    {
        $key = $this->collection_css->search(function($item,$key)use($name){
            return ($item['name'] == $name);
        });
        return $key ;
    }
    public function remove(string $name)
    {
        return $this->removeJs($name)->removeCss($name);
    }

    public function removeJs(string $name)
    {
        if(($key = $this->getCollectionJsKeyByName($name)) !== false){
            $this->collection_js->forget($key);
        }
        return $this;
    }

    public function removeCss(string $name)
    {
        if(($key = $this->getCollectionCssKeyByName($name)) !== false){
            $this->collection_css->forget($key);
        }
        return $this ;
    }

    public function render()
    {
        return $this->renderCss().$this->renderJs();
    }

    public function renderJs()
    {
        return $this->renderType('js','<script type="application/javascript" src="%s"></script>'."\n");
    }

    public function renderCss()
    {
        return $this->renderType('css','<link rel="stylesheet" type="text/css" href="%s">'."\n");

    }

    protected function renderType(string $type, string $tag){
        ob_start();

        $dependencies = [];
        foreach ($this->{'collection_'.$type}->all() as $resource){
            if(!$resource['library'] || $resource['linked'])
                $this->resolveResourceDependencies($resource['resource'],$dependencies);
        }

        foreach ($dependencies as $resource){
            echo sprintf($tag,$resource->url());
            if(($key = $this->{'getCollection'.ucfirst($type).'KeyByName'}($resource->getName())) !== false){
                $r = $this->{'collection_'.$type}->get($key);
                $r['rendered'] = true ;
                $this->{'collection_'.$type}->put($key,$r);
            }
        }

        foreach ($this->{'collection_'.$type}->all() as $resource){
            if(!$resource['rendered'] && (!$resource['library'] || $resource['linked'])){
                echo sprintf($tag,$resource['resource']->url());
            }
        }

        $content = ob_get_clean();
        return $content;
    }

    protected function resolveResourceDependencies(Resource $resource,array &$dependencies = [])
    {
        if($resource->hasDependencies()){
            foreach ($resource->getDependencies() as $dependency){
                array_unshift($dependencies,$dependency);
                $dependencies = array_unique($dependencies,SORT_REGULAR);
                $this->resolveResourceDependencies($dependency,$dependencies);
            }
        }

        return $dependencies;
    }

}