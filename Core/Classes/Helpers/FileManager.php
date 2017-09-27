<?php
namespace Core\Classes\Helpers;
use Core\Interfaces as Interfaces;
use Core\Classes\Data as CoreData;

class FileManager extends AbstractHelper {
	public function setSite($site) {
		parent::setSite($site);
		if (!$this->getSite()->getSiteConfig()['UPLOAD_PATH']) {
			throw new \RuntimeException("The upload path has not been configured!");
		}
	}

	public function getBaseFilePath() {
		return $this->getSite()->getSiteConfig()['UPLOAD_PATH'];
	}

	public function processBase64File($encodedFile,$fileName=null,$fileDirectory=null) {
		$temp = explode(",",$encodedFile);
		$fileTypeTemp = explode(':',$temp[0]);
		$fileType = explode(';',$fileTypeTemp[1])[0];
		$fileExtension = explode('/',$fileType)[1];

		$encodedFile = $temp[1];

		$uploadedFile = base64_decode($encodedFile);
		$uploadDir = $this->getBaseFilePath();
		if ($fileDirectory) {
			$uploadDir .= $fileDirectory.'/';
		}

		$this->createDirectory($uploadDir);

		if (!$fileName) {
			$fileName = sha1($uploadedFile.' '.time());
		}

		$file = fopen($uploadDir.$fileName,'w');
		if (!fwrite($file,$uploadedFile)) {
			throw new \RuntimeException("Error writing file: ".$uploadDir.$fileName);
		}
		fclose($file);
		return $fileName;
	}

	public function getDownloadableFileByFileName($fileName) {
		return $this->getDownloadableFile($this->getFileFromFileName($fileName));
	}

	public function getDownloadableFile(Interfaces\File $file) {
		$fileLocation = $this->getBaseFilePath().$file->getFullPath();
		header("Content-Type: ".mime_content_type($fileLocation));
		header("Content-Length: ".filesize($fileLocation));
		header("Content-Disposition: attachment; filename=".($file->getGloss() ? $file->getGloss():$file->getFileName()));
		readfile($fileLocation);
		exit;
	}

	public function removeFileByFileName($fileName) {
		return $this->removeFile($this->getFileFromFileName($fileName));
	}

	public function removeFile(Interfaces\File $file) {
		if (!unlink($this->getBaseFilePath().$file->getFullPath())) {
			throw new \RuntimeException("Error removing file: ".$this->getBaseFilePath().$file->getFullPath());
		}
		return true;
	}

	protected function createDirectory($directory) {
		if (!file_exists($directory)) {
		    if (!mkdir($directory, 0777, true)) {
				throw new \RuntimeException("Error creating directory: {$directory}");
			}
		}
	}

	public function getDirectoryContents($directoryPath=null,$filesOnly=false) {
		$pathNames = scandir($this->getBaseFilePath().$directoryPath);
		if (!$pathNames) {
			throw new \RuntimeException("Could not read directory: {$this->getBaseFilePath()}{$directoryPath}");
		}
		if ($filesOnly) {
			$pathNames = array_filter($pathNames,function($value) { return !is_dir($value);});
		}
		$contents = array();
		foreach ($pathNames as $path) {
			$contents[] = pathinfo($path);
		}
		return $contents;
	}

	public function getDirectoryFiles($directoryPath=null) {
		$contents = $this->getDirectoryContents($directoryPath,true);
		$files = array();
		foreach ($contents as $fileInfo) {
			$files[] = $this->getFileFromFileName($fileInfo['basename']);
		}
		return $files;
	}

	public function getFileFromFileName($fileName) {
		$filePath = $this->getBaseFilePath().$fileName;
		if (!is_file($filePath)) {
			throw new \RuntimeException("Could not find file: {$filePath}");
		}
		$fileInfo = pathinfo($this->getBaseFilePath().$fileName);
		return new CoreData\SimpleFile($fileInfo['filename'],null,$fileInfo['extension'],$fileInfo['basename']);
	}
}
?>