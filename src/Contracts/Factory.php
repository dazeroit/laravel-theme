<?php

namespace Dazeroit\Theme\Contracts;

/**
 * Interface Factory
 * @package Dazeroit\Theme\Contracts
 */
interface Factory
{
    /**
     * Creates a theme factory
     * @param string $theme
     * @return Factory
     */
    public static function make(string $theme):Factory;
}