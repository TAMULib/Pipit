<?php
namespace Core\Classes\Data;
use Core\Interfaces as Interfaces;
/**
 * Represents a file entry
 *
 * @author Jason Savell <jsavell@library.tamu.edu>
 */
class SimpleFile implements Interfaces\File {
	/** @var string $fileName The file name */
	private $fileName;
	/** @var string $filePath The path to the file */
	private $filePath;
	/** @var string $fileType The file type */
	private $fileType;
	/** @var string $gloss A display friendly name for the file */
	private $gloss;

	/** 
	*	@param string $fileName The file name
	*	@param string $filePath The path to the file. Optional
	*	@param string $fileType The file type. Optional
	*	@param string $gloss A display friendly name for the file. Optional
	*/
	public function __construct($fileName,$filePath=null,$fileType=null,$gloss=null) {
		$this->setFileName($fileName);
		$this->setFilePath($filePath);
		$this->setFileType($fileType);
		$this->setGloss($gloss);
	}

	/** 
	*	@param string $fileName Set the file name
	*	@return void
	*/
	protected function setFileName($fileName) {
		$this->fileName = $fileName;
	}

	/** 
	*	@param string $filePath Set the file path
	*	@return void
	*/
	protected function setFilePath($filePath) {
		$this->filePath = $filePath;
	}

	/** 
	*	@param string $fileType Set the file type
	*	@return void
	*/
	protected function setFileType($fileType) {
		$this->fileType = $fileType;
	}

	/** 
	*	@param string $gloss Set the display friendly gloss for the file
	*	@return void
	*/
	protected function setGloss($gloss) {
		$this->gloss = $gloss;
	}

	/** 
	* 	Get the file name
	*	@return string
	*/
	public function getFileName() {
		return $this->fileName;
	}

	/** 
	* 	Get the file path
	*	@return string
	*/
	public function getFilePath() {
		return $this->filePath;
	}

	/** 
	* 	Get the file type
	*	@return string|null
	*/
	public function getFileType() {
		return $this->fileType;
	}

	/** 
	* 	Get the file gloss
	*	@return string|null
	*/
	public function getGloss() {
		return $this->gloss;
	}

	/** 
	* 	Get the full path to the file on the file system
	*	@return string
	*/
	public function getFullPath() {
		$fullPath = $this->getFilePath();
		if ($fullPath) {
			$fullPath .= '/';
		}
		return $fullPath.$this->getGloss();
	}
}
