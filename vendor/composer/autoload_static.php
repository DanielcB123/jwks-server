<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1b77633c8e35b65ff9245360e1e52978
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'B' => 
        array (
            'Burge\\JwksServer\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Burge\\JwksServer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1b77633c8e35b65ff9245360e1e52978::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1b77633c8e35b65ff9245360e1e52978::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1b77633c8e35b65ff9245360e1e52978::$classMap;

        }, null, ClassLoader::class);
    }
}
