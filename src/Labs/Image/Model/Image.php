<?php

namespace Labs\Image\Model;

use Exception;
use Imagick;

class Image {

    /**
     * @var Imagick
     */
    protected $imageSource;

    /**
     * @var string
     */
    protected $path;

    /**
     *
     * @var string
     */
    protected $mimeType;

    /**
     * @param string $path absolute pathname
     */
    public function __construct($path) {
        $this->path = $path;
        $this->setSource($path);
    }

    public function setMimeType() {
        if (!file_exists($this->path)) {
            throw new Exception('path does not exist');
        }
        if (!$this->mimeType) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // TODO: possibly a security risk?
            $mimeType = finfo_file($finfo, $this->path);
            finfo_close($finfo);
            $this->mimeType = $mimeType;
        }
    }

    public function getMimeType() {
        return $this->mimeType;
    }

    /**
     * Created a new Imagic source and determines mimetype again.
     *
     * If path changes, run this method.
     *
     * @param string $path
     * @return boolean
     */
    public function setSource($path) {
        $this->imageSource = new Imagick($path);
        $this->setMimeType();
        return true;
    }

    /**
     * @return Imagick
     */
    public function getSource() {
        return $this->imageSource;
    }

}