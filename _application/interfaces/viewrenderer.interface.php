<?php
interface viewrenderer {
	public function renderView();
	public function setViewVariables($data);
	public function registerViewVariable($name,$data);
	public function getViewVariables();
	public function getViewVariable($name);
}
?>