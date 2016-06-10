<?php
namespace App;

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
 				$site->getViewRenderer()->registerViewVariable("widgets",$twidgets->search($data['term']));
				$viewName = "widgets.list";
			} else {
				$site->addSystemError('There was an error with the search');
			}
		break;
		case 'remove':
			if (isset($data['id']) && is_numeric($data['id']) && $twidgets->removeById($data['id'])) {
				$site->addSystemMessage('Widget removed');
			} else {
				$site->addSystemError('Error removing widget');
			}
		break;
		case 'insert':
			if (isset($data['widget']) && $twidgets->add($data['widget'])) {
				$site->addSystemMessage('Widget added');
			} else {
				$site->addSystemError('Error adding widget');
			}
		break;
		case 'update':
			if (isset($data['widget']) && (isset($data['id']) && is_numeric($data['id'])) && $twidgets->update($data['id'],$data['widget'])) {
				$site->addSystemMessage('Widget updated');
			} else {
				$site->addSystemError('Error updating widget');
			}
		break;
		case 'add':
			$page['subtitle'] = 'New Widget';
			$viewName = "widgets.add";
		break;
		case 'edit':
			$page['subtitle'] = 'Update Widget';
			if (isset($data['id']) && is_numeric($data['id']) && ($widget = $twidgets->getById($data['id']))) {
				$widget = $site->getViewRenderer()->registerViewVariable("widget",$widget);
				$viewName = "widgets.edit";
			}
		break;
	}
} else {
	$page['subtitle'] = 'Widgets';
	$site->getViewRenderer()->registerViewVariable("widgets", $twidgets->get());
	$viewName = "widgets.list";
}

?>