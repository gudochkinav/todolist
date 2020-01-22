<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit225c2fd2cdf3ec21e0c879258899a545
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TodoApp\\' => 8,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TodoApp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/lib',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'App\\Controllers\\AppController' => __DIR__ . '/../..' . '/app/Controllers/AppController.php',
        'TodoApp\\Application' => __DIR__ . '/../..' . '/src/lib/Application.php',
        'TodoApp\\Router' => __DIR__ . '/../..' . '/src/lib/Router.php',
        'TodoApp\\View\\View' => __DIR__ . '/../..' . '/src/lib/View/View.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit225c2fd2cdf3ec21e0c879258899a545::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit225c2fd2cdf3ec21e0c879258899a545::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit225c2fd2cdf3ec21e0c879258899a545::$classMap;

        }, null, ClassLoader::class);
    }
}
