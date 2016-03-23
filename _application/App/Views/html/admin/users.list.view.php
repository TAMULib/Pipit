<?php
$users = $parameters['users'];
echo '<div class="do-results">';
if ($users) {
	echo '<table class="list">
				<tr>
					<th>Last Name</th>
					<th>First Name</th>
					<th>Email</th>
					<th>Actions</th>
				</tr>';
	foreach ($users as $user) {
		if (!empty($user['inactive'])) {
			$rowClass = ' class="inactive"';
			$enableToggle = 'enable';
		} else {
			$rowClass = null;
			$enableToggle = 'disable';
		}
		echo "<tr{$rowClass}>
					<td>{$user['name_last']}</td>
					<td>{$user['name_first']}</td>
					<td>{$user['email']}</td>
					<td class=\"capitalize\">
						<a class=\"inline-block button button-small do-loadmodal\" href=\"{$app_http}?action=edit&id={$user['id']}\">Edit</a>";
echo '					<form class="inline-block do-remove" name="removewidget" method="POST" action="'.$app_http.'">
							<input type="hidden" name="action" value="'.$enableToggle.'" />
							<input type="hidden" name="id" value="'.$user['id'].'" />
							<input class="small capitalize" type="submit" name="submitremove" value="'.$enableToggle.'" />
						</form>';
echo "
					</td>
				</tr>";
	}
	echo '</table>';
} else {
	echo 'No users, yet!';
}
echo '</div>';
?>