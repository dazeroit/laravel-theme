<?php

namespace Dazeroit\Theme\Contracts;

/**
 * Interface Theme
 * @package Dazeroit\Theme\Contracts
 */
interface Theme
{
    /**
     * Prepare a theme.
     * If the theme does not exist, an exception will be thrown.
     * @param string $theme
     * @return mixed
     */
    public function prepare(string $theme);

    /**
     * Uses a theme.
     * If the theme does not exist, an exception will be thrown.
     * @param string $theme
     * @return mixed
     */
    public function uses(string $theme):ThemeFactory;

    /**
     * Returns the current theme factory.
     * If the theme is not prepared, an exception will be thrown.
     * @return ThemeFactory
     */
    public function current():ThemeFactory;

    /**
     * Exchange the theme among all those prepared.
     * If the theme is not prepared, an exception will be thrown.
     * @param string $theme
     * @return mixed
     */
    public function switch(string $theme);

    /**
     * Checks if the theme directory exists.
     * @param string $theme
     * @return bool
     */
    public function exists(string $theme):bool;

    /**
     * Check if the theme has been prepared.
     * @param string $theme
     * @return bool
     */
    public function has(string $theme):bool;

    /**
     * Returns a property of the manifest theme.
     * If the property is not set, the default value will be returned.
     * @param string $theme
     * @param string $property
     * @param null $default
     * @return mixed
     */
    public function info(string $theme,string $property,$default = null);

    /**
     * Removes a theme among those prepared.
     * If the theme is not prepared, an exception will be thrown.
     * @param string $theme
     * @return mixed
     */
    public function remove(string $theme);

    /**
     * Eliminates all the prepared themes.
     * @return mixed
     */
    public function reset();
}