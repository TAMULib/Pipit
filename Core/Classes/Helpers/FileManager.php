<?php
namespace Core\Classes\Helpers;
use Core\Interfaces as Interfaces;
use Core\Classes\Data as CoreData;

class FileManager extends AbstractHelper {
	/** @var string $baseFilePath The directory path to store uploaded files */
	private $baseFilePath;
	/**
	 * @param \Core\Interfaces\Site $site An implementation of \Interfaces\Site
	 * @return void
	 */
	public function configure(Interfaces\Site $site) {
		parent::configure($site);
		if (!is_array($this->getSite()->getSiteConfig()) || !is_string($this->getSite()->getSiteConfig()['UPLOAD_PATH'])) {
			throw new \RuntimeException("The upload path has not been configured!");
		}
		$this->baseFilePath = $this->getSite()->getSiteConfig()['UPLOAD_PATH'];
	}

	/**
	 * @return string
	 */
	public function getBaseFilePath() {
		return $this->baseFilePath;
	}

	/**
	 * @param string $encodedFile The base64 representation of the file data
	 * @param string $fileName The fileName to create. Optional
	 * @param string $fileDirectory The directory in which to create the file. Optional
	 * @return string The name of the created file
	 */
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

		if (!($file = fopen($uploadDir.$fileName,'w'))) {
			throw new \RuntimeException("Error opening file: ".$uploadDir.$fileName);
		}
		if (!fwrite($file,$uploadedFile)) {
			throw new \RuntimeException("Error writing file: ".$uploadDir.$fileName);
		}
		fclose($file);
		return $fileName;
	}

	/**
	 * Provides the named file as a downloadable attachment
	 * @param string $fileName The name of the file to retrieve
	 * @return void
	 */
	public function getDownloadableFileByFileName($fileName) {
		$this->getDownloadableFile($this->getFileFromFileName($fileName));
	}

	/**
	 * Provides the specified \Interfaces\File as a downloadable attachment
	 * @param \Core\Interfaces\File $file An \Interfaces\File
	 * @return void
	 */
	public function getDownloadableFile(Interfaces\File $file) {
		$fileLocation = $this->getBaseFilePath().$file->getFullPath();
		$this->checkFile($fileLocation);
		header("Content-Type: ".mime_content_type($fileLocation));
		header("Content-Length: ".filesize($fileLocation));
		header("Content-Disposition: attachment; filename=".($file->getGloss() ? $file->getGloss():$file->getFileName()));
		readfile($fileLocation);
		exit;
	}

	/**
	 * Attempts to delete a file with the given file name
	 * @param string $fileName
	 * @return boolean Returns true on successful deletion
	 */
	public function removeFileByFileName($fileName) {
		return $this->removeFile($this->getFileFromFileName($fileName));
	}

	/**
	 * Attempts to delete a file represented by a \Core\Interfaces\File implementation
	 * @param \Core\Interfaces\File $file The File to delete
	 * @return boolean Returns true on successful deletion
	 */
	public function removeFile(Interfaces\File $file) {
		if (!unlink($this->getBaseFilePath().$file->getFullPath())) {
			throw new \RuntimeException("Error removing file: ".$this->getBaseFilePath().$file->getFullPath());
		}
		return true;
	}

	/**
	 * Attempts to create a directory with the given name
	 * @param string $directory The name of the directory to create
	 * @return void
	 */
	protected function createDirectory($directory) {
		if (!file_exists($directory)) {
		    if (!mkdir($directory, 0777, true)) {
				throw new \RuntimeException("Error creating directory: {$directory}");
			}
		}
	}

	/**
	 * Provides an array of \pathinfo results for the files in the given directory
	 * @param string $directoryPath The directory to scan
	 * @param boolean $filesOnly Whether to exclude subdirectories from the results
	 * @return array<int, array<string, string>>
	 */
	public function getDirectoryContents($directoryPath=null,$filesOnly=false) {
		$scanDirectory = $this->getBaseFilePath().$directoryPath;
		$pathNames = scandir($scanDirectory);
		if (!$pathNames) {
			throw new \RuntimeException("Could not read directory: {$scanDirectory}");
		}

		if ($filesOnly) {
			$pathNames = array_filter($pathNames,function($value) use ($scanDirectory) { return !is_dir($scanDirectory.$value);});
		}
		$contents = array();
		foreach ($pathNames as $path) {
			$contents[] = pathinfo($path);
		}
		return $contents;
	}

	/**
	 * Provides an array of \Core\Interfaces\File for a given directory
	 * @param string $directoryPath The directory whose files should be retrieved
	 * @return \Core\Interfaces\File[]
	 */
	public function getDirectoryFiles($directoryPath=null) {
		$contents = $this->getDirectoryContents($directoryPath,true);
		$files = array();
		foreach ($contents as $fileInfo) {
			$files[] = $this->getFileFromFileName($fileInfo['basename']);
		}
		return $files;
	}

	/**
	 * Provides a \Core\Interfaces\File for a given file name
	 * @param string $fileName The name of the file to retrieve
	 * @return \Core\Classes\Data\SimpleFile
	 */
	public function getFileFromFileName($fileName) {
		$filePath = $this->getBaseFilePath().$fileName;
		$this->checkFile($filePath);
		$fileInfo = pathinfo($this->getBaseFilePath().$fileName);
		return new CoreData\SimpleFile($fileInfo['filename'],null,array_key_exists('extension',$fileInfo) ? $fileInfo['extension']:null,$fileInfo['basename']);
	}

	/**
	 * @param string $filePath Checks for the existence of a full file path
	 * @return void
	 */
	private function checkFile($filePath) {
		if (!is_file($filePath)) {
			throw new \RuntimeException("Could not find file: {$filePath}");
		}
	}
}
