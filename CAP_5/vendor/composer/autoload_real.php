<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit85186e9feae9ac5081b645b975b7411d
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit85186e9feae9ac5081b645b975b7411d', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit85186e9feae9ac5081b645b975b7411d', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit85186e9feae9ac5081b645b975b7411d::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
