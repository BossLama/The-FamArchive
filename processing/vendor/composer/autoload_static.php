<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit424a08ef5f2c727d7f3f1cafbdd05a89
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'FamArchive\\Models\\' => 18,
            'FamArchive\\Actions\\' => 19,
            'FamArchive\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'FamArchive\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/models',
        ),
        'FamArchive\\Actions\\' => 
        array (
            0 => __DIR__ . '/../..' . '/actions',
        ),
        'FamArchive\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit424a08ef5f2c727d7f3f1cafbdd05a89::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit424a08ef5f2c727d7f3f1cafbdd05a89::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit424a08ef5f2c727d7f3f1cafbdd05a89::$classMap;

        }, null, ClassLoader::class);
    }
}
