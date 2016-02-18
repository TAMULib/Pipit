<?php
$user = $parameters['user'];
echo '<form class="do-submit" name="updateuser" method="POST" action="'.$app_http.'">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="id" value="'.$user['id'].'" />
			<div class="column column-half">
				<label for="user[name_last]">Last Name</label>
				<input type="text" name="user[name_last]" value="'.$user['name_last'].'" />
				<label for="user[name_first]">First Name</label>
				<input type="text" name="user[name_first]" value="'.$user['name_first'].'" />
				<label for="user[email]">Email</label>
				<input type="text" name="user[email]" value="'.$user['email'].'" />
			</div>
			<input type="submit" name="submituser" value="Update User" />
		</form>';
?>