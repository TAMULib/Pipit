<?php
namespace Core\Interfaces;

interface File {
	/**
	 * @return string
	 */
	public function getFileName();
	/**
	 * @return string
	 */
	public function getFilePath();
	/**
	 * @return string
	 */
	public function getFileType();
	/**
	 * @return string
	 */
	public function getFullPath();
	/**
	 * @return string
	 */
	public function getGloss();
}