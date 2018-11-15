<?php

namespace Dazeroit\Theme\Contracts;

/**
 * Interface ThemeFactory
 * @package Dazeroit\Theme\Contracts
 */
interface ThemeFactory
{
    /**
     * Creates a theme factory
     * @param string $theme
     * @return ThemeFactory
     */
    public static function make(string $theme):ThemeFactory;

    /**
     * Returns the encoded manifest theme json.
     * NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit.
     * @return mixed
     */
    public function manifest();

    /**
     * Returns a property of the manifest theme.
     * If the property is not set, the default value will be returned.
     * @param string $property
     * @param null $default
     * @return mixed
     */
    public function info(string $property,$default = null);

    /**
     * Returns the theme name of this factory
     * @return string
     */
    public function getTheme():string;

}