<?php

namespace Dazeroit\Theme\Tests;

use Orchestra\Testbench\TestCase;

class TestBasic extends TestCase
{

    public function basic(){
        return $this->assertTrue(true);
    }

    protected function getPackageProviders($app)
    {
        return [
            Dazeroit\Theme\ThemeServiceProvider::class,
        ];
    }
}