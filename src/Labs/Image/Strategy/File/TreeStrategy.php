<?php

namespace Labs\Image\Strategy\File;

use Exception;

class TreeStrategy extends \Labs\Image\Strategy\FileStrategy {

    protected $contentDir;
    protected $depth;
    protected $storeVariants;

    public function __construct($contentDir, $depth, $storeVariants = false) {
        $endsInDS = $contentDir[strlen($contentDir)-1] === DIRECTORY_SEPARATOR;
        if ($endsInDS) {
            throw new Exception('Option "content_directory" cannot end with directory separator : "' . $contentDir. '"');
        }
        if (!in_array($depth, array(1, 2, 3))) {
                throw new Exception('Option "depth" must be between 1 and 3 : "'. $depth .'"');
        }

        $this->contentDir = $contentDir;
        $this->depth = $depth;
        $this->storeVariants = $storeVariants;
    }

    public function save($imageName) {
        $baseName = pathinfo($imageName, PATHINFO_BASENAME);
        $targetPath = $this->pathService->getOriginalPath($baseName);
        $this->createPath($targetPath);
        
        if (!rename($oldname, $newname)){
            throw new Exception('Error moving file into tree');
        }

        return $baseName;
    }

    /**
     * Checks if directory is empty.
     *
     * @param string $path
     * @return boolean
     * @throws Exception
     */
    public function isDirEmpty($path) {
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

    /**
     * Gets the path to where the orignal file should be.
     *
     * @param string $fileName
     * @return string
     * @throws Exception
     */
    public function getOriginalPathFilename($fileName) {
        $path = $this->contentDir . DIRECTORY_SEPARATOR;
        $number = 0;
        $depthPath = '';
        for ($i = 1; $i <= $this->depth; $i++) {
            // TODO: does not account for extension
            $depthPath .= substr($fileName, $number, 2) . DIRECTORY_SEPARATOR;
            $number = $number + 2;
        }
        return $path . $depthPath . $fileName;
    }

    /**
     * Gets calculated path for an fileName.
     *
     * This path is where the original file can be or are stored.
     * Note that the path does NOT need to actually exist.
     *
     * @param string $fileName
     * @return string path to original file
     */
    public function getOriginalPath($fileName) {
        $path = $this->getContentPath() . DIRECTORY_SEPARATOR;
        $number = 0;
        $depthPath = '';
        for ($i = 1; $i <= $this->getDepth(); $i++) {
            // TODO: does not account for extension
            $depthPath .= substr($fileName, $number, 2) . DIRECTORY_SEPARATOR;
            $number = $number + 2;
        }
        return $path . $depthPath;
    }

    /**
     * Gets calculated path for an fileName.
     * This path is where an edited file can be or are stored.
     * Note that the path does NOT need to actually exist.
     *
     * @param string $fileName
     * @return string path to edited file
     */
    public function getEditPath($fileName) {
        if (strpos($fileName, DIRECTORY_SEPARATOR) !== false) {
            throw new Exception('filename cannot contain path');
        }
        $path = $this->getOriginalPath($fileName);
        if ($this->config['image']['tree']['store_resized'] == true) {
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            return $path . $baseName . DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    /**
     * @param string $fileName
     * @return boolean
     */
    public function delete($fileName) {

        // Delete original file.
        unlink($this->getOriginalPathFilename($fileName));

        // Delete edit paths if applicable.
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $originalPath = $this->getOriginalPath($fileName);

        if ($this->config['image']['tree']['store_resized'] == true) {
            $editPath = $this->getEditPath($fileName);
            if ($editPath != $originalPath) {
                $files = glob($editPath . $baseName .'*');
                foreach ($files as $file) {
                    unlink($file);
                }
                if (file_exists($editPath)) {
                    rmdir($editPath);
                }
            }
        }

        // Delete parent path if this file was the only one there.
        if ($this->config['image']['tree']['directory_depth'] !== 0) {
            if ($this->isDirEmpty($originalPath)) {
                rmdir($originalPath);
            }
        }
        return true;
    }

    /**
     *
     * @param string $path
     * @return boolean
     * @throws Exception
     */
    public function createPath($path) {
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new Exception('Could not create path');
            }
        }
        return true;
    }

    /**
     *
     * @param string $path
     * @return string new path
     * @throws Exception
     */
    public function moveToOriginalPath($path) {
        $baseName = pathinfo($path, PATHINFO_BASENAME);
        $newFilePath = $this->getOriginalPathFilename($baseName);
        if (!rename($path, $newFilePath)) {
            throw new Exception('Could not rename file');
        }
        return $newFilePath;
    }

}