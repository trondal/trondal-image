<?php

namespace Labs\Image\Strategy\File;

use Exception;
use Imagick;
use Labs\Image\Model\Image;
use Labs\Image\Strategy\FileStrategy;

/**
 * I have kept all methods as atomic as possible, otherwise it will be very confusing
 * That was a design decicion.
 */

class SimpleStrategy extends FileStrategy {

    protected $contentDir;
    protected $storeVariants;

    public function __construct($contentDir, $storeVariants = false) {
        $endsInDS = $contentDir[strlen($contentDir)-1] === DIRECTORY_SEPARATOR;
        if ($endsInDS) {
            throw new Exception('Option "content_directory" cannot end with directory separator : "' . $contentDir. '"');
        }
        $this->contentDir = $contentDir;
        $this->storeVariants = $storeVariants;
    }

    public function get($imageName) {

        $imagePath = $this->contentDir . DIRECTORY_SEPARATOR . $imageName;
        if (!file_exists($imagePath)) {
            return false;
        }
        return new Image($imagePath);
    }

    public function getVariant($imageName, $width) {
        $imagePath = $this->contentDir . DIRECTORY_SEPARATOR . $imageName;

        $fileName = pathinfo($imageName, PATHINFO_FILENAME);
        $extension = pathinfo($imageName, PATHINFO_EXTENSION);

        $variantDir = $this->contentDir;
        if ($this->storeVariants == true) {
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            $variantDir = $this->contentDir . DIRECTORY_SEPARATOR. $baseName;
        }

        $newFileName = $variantDir . DIRECTORY_SEPARATOR . $fileName . '_' . $width . '.'. $extension;

        // Resized image already exists, return that.
        if (file_exists($newFileName)) {
            return new Image($newFileName);
        }

        // make sure the variantdir exists, or are created
        if (!file_exists($variantDir)) {
            $this->createPath($variantDir);
        }

        $originalImage = new Imagick($imagePath);
        $srcWidth = $originalImage->getImageWidth();
        $srcHeight = $originalImage->getImageHeight();
        $ratio = $width / $srcWidth;
        $height = $srcHeight * $ratio;

        $images = $originalImage->coalesceImages();
        foreach ($images as $frame) {
            $frame->scaleImage($width, $height);
        }
        $images->writeImages($newFileName, true);

        $image = new Image($newFileName);
        if ($this->storeVariants === false) {
            unlink($newFileName);
        }
        return $image;
    }

    public function delete($fileName) {
        // delete variants first
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $variantDir = $this->contentDir . DIRECTORY_SEPARATOR . $baseName . DIRECTORY_SEPARATOR;

        $files = glob($variantDir . '*');
        // delete all files in dir
        foreach ($files as $file) {
            unlink($file);
        }
        // remove variant dir
        if (is_dir($variantDir)) {
            rmdir($variantDir);
        }
        // delete original file
        unlink($this->contentDir . DIRECTORY_SEPARATOR . $fileName);
        return true;
    }

    public function save($imageName) {
        $baseName = pathinfo($imageName, PATHINFO_BASENAME);
        return $baseName;
    }

}
