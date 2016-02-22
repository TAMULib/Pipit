<?php
namespace TAMU\Seed;

$page['title'] = 'Manage Widgets';
$page['navigation'] = array(
						array("name"=>"list"),
						array("name"=>"add","action"=>"add","modal"=>true));
$page['search'] = true;
$twidgets = new Classes\Data\Widgets();

if (isset($data['action'])) {
	switch ($data['action']) {
		case 'search':
			if (isset($data['term'])) {
 				$viewRenderer->registerViewVariable("widgets",$twidgets->search($data['term']));
				$viewName = "widgets.list";
			} else {
				$system[] = 'There was an error with the search';
			}
		break;
		case 'remove':
			if (isset($data['id']) && is_numeric($data['id']) && $twidgets->removeById($data['id'])) {
				$system[] = 'Widget removed';
			} else {
				$system[] = 'Error removing widget';
			}
		break;
		case 'insert':
			if (isset($data['widget']) && $twidgets->add($data['widget'])) {
				$system[] = 'Widget added';
			} else {
				$system[] = 'Error adding widget';
			}
		break;
		case 'update':
			if (isset($data['widget']) && (isset($data['id']) && is_numeric($data['id'])) && $twidgets->update($data['id'],$data['widget'])) {
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
			if (isset($data['id']) && is_numeric($data['id']) && ($widget = $twidgets->getById($data['id']))) {
				$widget = $viewRenderer->registerViewVariable("widget",$widget);
				$viewName = "widgets.edit";
			} else {
				echo 'no stuff';
			}
		break;
	}
} else {
	$page['subtitle'] = 'Widgets';
	$viewRenderer->registerViewVariable("widgets", $twidgets->get());
	$viewName = "widgets.list";
}
$viewRenderer->setPage($page);

?>