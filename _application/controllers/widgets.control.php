<?php
$page['title'] = 'Manage Widgets';
$page['navigation'] = array(
						array("name"=>"list"),
						array("name"=>"add","action"=>"add","modal"=>true));
$page['search'] = true;
$twidgets = new widgets();

if (isset($data['action'])) {
	switch ($data['action']) {
		case 'search':
			if (isset($data['term'])) {
 				$viewRenderer->registerViewVariable("widgets",$twidgets->searchWidgetsBasic($data['term']));
				$viewName = "widgets";
			} else {
				$system[] = 'There was an error with the search';
			}
		break;
		case 'remove':
			if (isset($data['id']) && is_numeric($data['id']) && $twidgets->removeWidget($data['id'])) {
				$system[] = 'Widget removed';
			} else {
				$system[] = 'Error removing widget';
			}
		break;
		case 'insert':
			if (isset($data['widget']) && $twidgets->insertWidget($data['widget'])) {
				$system[] = 'Widget added';
			} else {
				$system[] = 'Error adding widget';
			}
		break;
		case 'update':
			if (isset($data['widget']) && (isset($data['id']) && is_numeric($data['id'])) && $twidgets->updateWidget($data['id'],$data['widget'])) {
				$system[] = 'Widget updated';
			} else {
				$system[] = 'Error updating widget';
			}
		break;
		case 'add':
			$page['subtitle'] = 'New Widget';
			$viewName = "widgets.add";
		break;
		case 'edit':
			$page['subtitle'] = 'Update Widget';
			if (isset($data['id']) && is_numeric($data['id']) && ($widget = $twidgets->getWidgetById($data['id']))) {
				$widget = $viewRenderer->registerViewVariable("widget",$widget);
				$viewName = "widgets.edit";
			} else {
				echo 'no stuff';
			}
		break;
	}
} else {
	$page['subtitle'] = 'Widgets';
	$viewRenderer->registerViewVariable("widgets", $twidgets->getWidgets());
	$viewName = "widgets.list";
}
$viewRenderer->setPage($page);

?>