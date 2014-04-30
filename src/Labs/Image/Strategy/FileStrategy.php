<?php

namespace Labs\Image\Strategy;

class FileStrategy extends \Labs\Image\Strategy\AbstractStrategy {

    /**
     *
     * @param string $path
     * @return boolean
     * @throws Exception
     */
    protected function createPath($path) {
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true)) {
                throw new Exception('Could not create path');
            }
        }
        return true;
    }

    /**
     *
     * @param string $path
     * @return boolean
     */
    public function hasTransparency($path) {
        $values = array();
        exec("convert ". escapeshellcmd($path) ." -format '%[opaque]' info:", $values);
        if (in_array("false", $values)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if the image is an animated GIF.
     *
     * An animated GIF has multiple image frames. Of course not all multiple
     * frame GIF images are ment to be animated. I sometimes use it as a
     * image archive for non-animation purposes. But that is a special case.
     * In general the below is true.
     * -- Anthony Thyssen, Webmaster for ImageMagick Example Pages
     *
     * Also even if this method also "works" on non-gif formats, it cannot
     * detect animated png as imagick does not support APNG. Hence the
     * method name.  Trond
     *
     * @param string $path
     * @return boolean
     */
    public static function isAnimatedGif($path) {
        $result = exec('identify -format %n ' . escapeshellcmd($path));
        if ($result === '1') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks if directory is empty.
     *
     * @param string $path
     * @return boolean
     * @throws Exception
     */
    protected function isDirEmpty($path) {
        if (!is_readable($path)) {
            throw new Exception('dir is not readable');
        }
        $handle = opendir($path);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return false;
            }
        }
        return true;
    }

}
