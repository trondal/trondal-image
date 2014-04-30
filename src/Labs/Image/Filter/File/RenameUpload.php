<?php

namespace Labs\Image\Filter\File;

class RenameUpload extends \Zend\Filter\File\RenameUpload {

    protected function applyRandomToFilename($source, $filename) {
        $info = pathinfo($filename);
        $filename = sha1($info['filename'] . microtime());
        if (isset($info['extension'])) {
            $filename .= '.' . $info['extension'];
        }
        return $filename;
    }

}