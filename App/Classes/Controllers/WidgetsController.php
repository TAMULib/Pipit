<?php
namespace App\Classes\Controllers;
use App\Classes\Data as AppClasses;

class WidgetsController {
	private $site;
	private $widgetsRepo;
	private $page = array();
	private $viewName;

	public function __construct(&$site) {
		$this->site = $site;
		$this->widgetsRepo = new AppClasses\Widgets();
		$page['title'] = 'Manage Widgets';
		$page['navigation'] = array(
								array("name"=>"list"),
								array("name"=>"add","action"=>"add","modal"=>true));
		$page['search'] = true;
		$this->page = $page;
	}

	private function setViewName($viewName) {
		$this->viewName = $viewName;
	}

	public function evaluate() {
		$data = $this->site->getSanitizedInputData();
		if (!empty($data['action'])) {
			$methodName = $data['action'];
			if (!empty($data['subaction'])) {
				$methodName = $methodName.$data['subaction'];
			}
			if (method_exists($this,$methodName)) {
				$this->$methodName();
			}
		} else {
			$this->loadDefault();
		}
		$this->site->getViewRenderer()->setPage($this->page);
		if (!empty($this->viewName)) {
			$this->site->getViewRenderer()->setView($this->viewName);
		}
	}

	protected function parts() {
		$data = $this->site->getSanitizedInputData();
		$widget = $this->widgetsRepo->getById($data['widgetid']);
		$this->page['subtitle'] = 'Parts for '.$widget['name'];
		$this->site->getViewRenderer()->registerViewVariable("widget",$widget);
		$this->site->getViewRenderer()->registerViewVariable("parts",$this->widgetsRepo->getPartsByWidgetId($data['widgetid']));
		$this->setViewName('widgets.parts');
	}

	protected function remove() {
		$data = $this->site->getSanitizedInputData();
		if (isset($data['id']) && is_numeric($data['id']) && $this->widgetsRepo->removeById($data['id'])) {
			$this->site->addSystemMessage('Widget removed');
		} else {
			$this->site->addSystemError('Error removing widget');
		}		
	}

	protected function insert() {
		$data = $this->site->getSanitizedInputData();
		if (isset($data['widget']) && $this->widgetsRepo->add($data['widget'])) {
			$this->site->addSystemMessage('Widget added');
		} else {
			$this->site->addSystemError('Error adding widget');
		}
	}

	protected function add() {
		$this->page['subtitle'] = 'New Widget';
		$this->site->getViewRenderer()->setView("widgets.add");
		$this->setViewName('widgets.add');
	}

	protected function update() {
		$data = $this->site->getSanitizedInputData();
		if (isset($data['widget']) && (isset($data['id']) && is_numeric($data['id'])) && $this->widgetsRepo->update($data['id'],$data['widget'])) {
			$this->site->addSystemMessage('Widget updated');
		} else {
			$this->site->addSystemError('Error updating widget');
		}
	}

	protected function edit() {
		$this->page['subtitle'] = 'Update Widget';
		$data = $this->site->getSanitizedInputData();
		if (isset($data['id']) && is_numeric($data['id']) && ($widget = $this->widgetsRepo->getById($data['id']))) {
			$widget = $this->site->getViewRenderer()->registerViewVariable("widget",$widget);
			$this->setViewName('widgets.edit');
		}
	}

	protected function partsAdd() {
		$data = $this->site->getSanitizedInputData();
		if ($this->widgetsRepo->addPartToWidget($data['widgetid'],$data['part'])) {
			$this->site->addSystemMessage('Part added');
		} else {
			$this->site->addSystemError('There was an error adding the part');
		}
	}

	protected function partsRemove() {
		$data = $this->site->getSanitizedInputData();
		if ($this->widgetsRepo->removePartById($data['partid'])) {
			$this->site->addSystemMessage('Part removed');
		} else {
			$this->site->addSystemError('There was an error removing the part');
		}		
	}

	protected function search() {
		$data = $this->site->getSanitizedInputData();
		if (isset($data['term'])) {
			$this->site->getViewRenderer()->registerViewVariable("widgets",$this->widgetsRepo->search($data['term']));
			$this->setViewName("widgets.list");
		} else {
			$site->addSystemError('There was an error with the search');
		}
	}

	protected function loadDefault() {
		$this->page['subtitle'] = 'Widgets';
		$this->site->getViewRenderer()->registerViewVariable("widgets", $this->widgetsRepo->get());
		$this->setViewName('widgets.list');
	}
}