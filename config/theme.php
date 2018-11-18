<?php

return [

    /*
    |--------------------------------------------------------------------------
    | dazeroit/laravel-theme configurations
    |--------------------------------------------------------------------------
    */

    'dev-env' => true,

    /*
    |--------------------------------------------------------------------------
    | Theme Path
    |--------------------------------------------------------------------------
    |
    | This value is the root path of all themes
    |
    */
    'path' => env('THEME_PATH','resources/themes/'),
    /*
    |--------------------------------------------------------------------------
    | Theme Namespace
    |--------------------------------------------------------------------------
    |
    | The namespace used to call the views
    | <namespace>:<theme-name>.<views>
    |
    */
    'namespace' => env('THEME_NAMESPACE','theme'),
    /*
    |--------------------------------------------------------------------------
    | Theme Promise
    |--------------------------------------------------------------------------
    |
    | The "promise" theme is used if no theme is prepared
    | Check the documentation for more information.
    |
    */
    'promise' => env('THEME_PROMISE','default'),
    /*
    |--------------------------------------------------------------------------
    | Theme Publish
    |--------------------------------------------------------------------------
    |
    | Publish section
    |
    |
    */
    'publish' => [
        /*
        |--------------------------------------------------------------------------
        | Publish Public Folder
        |--------------------------------------------------------------------------
        | The public folder
        | This is generally called "public"
        |
        */
        'public-folder' => 'public_html',
        /*
        |--------------------------------------------------------------------------
        | Publish path
        |--------------------------------------------------------------------------
        | The publish path to store assets files.
        | This is inside the "public" folder.
        |
        */
        'path' => env('THEME_PUBLISH_PATH','themes/'),
    ],
    /*
    |--------------------------------------------------------------------------
    | Theme Views
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'views' => [
        /*
        |--------------------------------------------------------------------------
        | Views folder
        |--------------------------------------------------------------------------
        | The folder that contains templates view
        | If you want to create sub-folders structure MUST use dot to separate folders
        */
        'folder' => 'views',
    ],
    /*
    |--------------------------------------------------------------------------
    | Theme Layouts
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'layouts' => [
        /*
        |--------------------------------------------------------------------------
        | Layouts folder
        |--------------------------------------------------------------------------
        | The folder that contains templates layouts
        | If you want to create sub-folders structure MUST use dot to separate folders.
        */
        'folder' => 'layouts',
    ],
    /*
    |--------------------------------------------------------------------------
    | Theme Partials
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'partials' => [
        /*
        |--------------------------------------------------------------------------
        | Partials folder
        |--------------------------------------------------------------------------
        | The folder that contains templates partial
        | If you want to create sub-folders structure MUST use dot to separate folders.
        */
        'folder' => 'partials',
    ],
    /*
    |--------------------------------------------------------------------------
    | Theme Assets
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'assets' => [
        /*
        |--------------------------------------------------------------------------
        | Assets path
        |--------------------------------------------------------------------------
        | The root path inside the theme folder
        | <themes:path>/<theme-name>/<assets:path>
        */
        'path' => 'assets/',
        /*
        |--------------------------------------------------------------------------
        | Assets folders
        |--------------------------------------------------------------------------
        | Folders to includes in the theme structure by default
        | If you want to create sub-folders structure MUST use dot to separate folders.
        | Example :
        |       'js.plugins.awesome'
        */
        'folders' => [
            'js',
            'css',
        ],
        /*
        |--------------------------------------------------------------------------
        | Assets npm
        |--------------------------------------------------------------------------
        |
        | This section is used to manage the automatically
        | installation of packages with the npm
        | Check the documentation for more information.
        |
        */
        'npm' => [
            /*
            |--------------------------------------------------------------------------
            | npm enable
            |--------------------------------------------------------------------------
            | Enable the npm functionality.
            | This ability enable to run npm installation process when a theme is installed.
            | Check the documentation for more information.
            |
            */
            'enable' => true,
            /*
            |--------------------------------------------------------------------------
            | npm flags
            |--------------------------------------------------------------------------
            | npm flags to include when 'npm install' is launched
            | Example :
            |       '--production'
            |       '--global'
            */
            'flags' => [
                 //'--production'
            ],
            /*
            |--------------------------------------------------------------------------
            | npm install
            |--------------------------------------------------------------------------
            | npm packages to install
            | npm install <flags>
            | this command will be execute only if this array
            | contains packages to install globally for each theme
            | Example :
            |       'axios',
            |       '--save-dev webpack@<version>'
            |       '--global typescript'
            */
            'install' => [
                // add some awesome packages

            ]
        ],
    ],

];