<?php
namespace Core\Classes\Data;
use Core\Interfaces as Interfaces;

/**
 * Represents a file entry
 *
 * @author Jason Savell <jsavell@library.tamu.edu>
 */
class SimpleFile implements Interfaces\File {
    private $fileName;
    private $filePath;
    private $fileType;
    private $gloss;

    public function __construct($fileName,$filePath,$fileType=null,$gloss=null) {
        $this->setFileName($fileName);
        $this->setFilePath($filePath);
        $this->setFileType($fileType);
        $this->setGloss($gloss);
    }

    protected function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    protected function setFilePath($filePath) {
        $this->filePath = $filePath;
    }

    protected function setFileType($fileType) {
        $this->fileType = $fileType;
    }

    protected function setGloss($gloss) {
        $this->gloss = $gloss;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function getFilePath() {
        return $this->filePath;
    }

    public function getFileType() {
        return $this->fileType;
    }

    public function getGloss() {
        return $this->gloss;
    }

    public function getFullPath() {
        $fullPath = $this->getFilePath();
        if ($fullPath) {
            $fullPath .= '/';
        }
        return $fullPath.$this->getGloss();
    }
}
