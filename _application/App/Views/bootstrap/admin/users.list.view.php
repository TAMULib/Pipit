<?php
$users = $parameters['users'];
echo '<div class="do-results">';
if ($users) {
	echo '<table class="table">
				<tr>
					<th>Last Name</th>
					<th>First Name</th>
					<th>Email</th>
					<th>Actions</th>
				</tr>';
	foreach ($users as $user) {
		if (!empty($user['inactive'])) {
			$rowClass = ' class="danger"';
			$enableToggle = 'enable';
		} else {
			$rowClass = null;
			$enableToggle = 'disable';
		}
		echo "<tr{$rowClass}>
					<td>{$user['name_last']}</td>
					<td>{$user['name_first']}</td>
					<td>{$user['email']}</td>
					<td class=\"capitalize\">";
if ($globalUser->getProfileValue("id") != $user['id']) {
	echo "				<a class=\"btn btn-default do-loadmodal\" href=\"{$app_http}?action=edit&id={$user['id']}\">Edit</a>";
} else {
	echo "				<a class=\"btn btn-default\" href=\"{$config['PATH_HTTP']}user.php?action=edit\">Edit</a>";
}
if ((((!empty($user['haspassword']) && $user['haspassword']) && $enableToggle == 'enable') || $enableToggle != 'enable') && $user['isadmin'] != 1) {
	echo '					<form class="inline-block do-submit-confirm" name="togglestatus" method="POST" action="'.$app_http.'">
								<input type="hidden" name="action" value="'.$enableToggle.'" />
								<input type="hidden" name="id" value="'.$user['id'].'" />
								<input class="btn btn-default capitalize" type="submit" name="submitstatus" value="'.$enableToggle.'" />
							</form>';
}
if (!$user['inactive'] && $user['isadmin'] == 0) {
	echo '					<form class="inline-block do-submit-confirm" name="elevateuser" method="POST" action="'.$app_http.'">
								<input type="hidden" name="action" value="update" />
								<input type="hidden" name="id" value="'.$user['id'].'" />
								<input type="hidden" name="user[isadmin]" value="1" />
								<input class="btn btn-default" type="submit" name="submituser" value="Make Admin" />
							</form>';
}
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