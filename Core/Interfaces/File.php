<?php
namespace Core\Interfaces;

interface File {
	public function getFileName();
	public function getFilePath();
	public function getFileType();
	public function getFullPath();
	public function getGloss();
}