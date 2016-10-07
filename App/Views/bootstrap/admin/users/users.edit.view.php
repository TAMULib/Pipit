<?php
$user = $parameters['user'];
echo '<form class="do-submit" name="updateuser" method="POST" action="'.$app_http.'">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="id" value="'.$user['id'].'" />
			<div class="form-group">
				<label for="user[name_last]">Last Name</label>
				<input class="form-control" type="text" name="user[name_last]" value="'.$user['name_last'].'" />
			</div>
			<div class="form-group">
				<label for="user[name_first]">First Name</label>
				<input class="form-control" type="text" name="user[name_first]" value="'.$user['name_first'].'" />
			</div>
			<div class="form-group">
				<label for="user[email]">Email</label>
				<input class="form-control" type="text" name="user[email]" value="'.$user['email'].'" />
			</div>
			<input class="btn btn-default" type="submit" name="submituser" value="Update User" />
		</form>';
?>