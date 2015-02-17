<?php
$out .= '<div class="do-results">';
if ($widgets) {
	$out .= '<table class="list">
				<tr>
					<th>Name</th>
					<th>Actions</th>
				</tr>';
	foreach ($widgets as $widget) {
		$out .= "<tr>
					<td>{$widget['name']}</td>
					<td class=\"capitalize\">
						<a class=\"do-loadmodal\" href=\"{$app_http}?action=edit&id={$widget['id']}\">Edit</a> | 
						<a class=\"do-remove\" href=\"{$app_http}?action=remove&id={$widget['id']}\">Remove</a>
					</td>
				</tr>";
	}
	$out .= '</table>';
} else {
	$out .= 'No widgets, yet!';
}
$out .= '</div>';
?>