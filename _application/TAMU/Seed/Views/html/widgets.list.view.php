<div class="do-results">
<?php
if ($parameters['widgets']) {
?>
	<table class="list">
		<tr>
			<th>Name</th>
			<th>Actions</th>
		</tr>
<?php
	foreach ($parameters['widgets'] as $widget) {
		echo "<tr>
					<td>{$widget['name']}</td>
					<td class=\"capitalize\">
						<a class=\"do-loadmodal\" href=\"{$app_http}?action=edit&id={$widget['id']}\">Edit</a> | 
						<a class=\"do-remove\" href=\"{$app_http}?action=remove&id={$widget['id']}\">Remove</a>
					</td>
				</tr>";
	}
?>
	</table>
<?php
} else {
	echo 'No widgets, yet!';
}
?>
</div>
