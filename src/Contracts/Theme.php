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
    public function uses(string $theme);

    /**
     * Returns the current theme factory.
     * If the theme is not prepared, an exception will be thrown.
     * @return Factory
     */
    public function current():Factory;

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
     * Returns the encoded manifest theme json.
     * NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit.
     * @param string $theme
     * @return mixed
     */
    public function manifest(string $theme);

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