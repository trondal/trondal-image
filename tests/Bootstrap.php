<?php

namespace Labs\ImageTest;

clearstatcache();
error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class Bootstrap {

    public static function init() {
        static::initAutoloader();
    }

    protected static function initAutoloader() {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            include $vendorPath . '/autoload.php';
            return;
        }
    }

    protected static function findParentPath($path) {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir)
                return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }

}

Bootstrap::init();