<?php
echo '<form class="do-submit" name="adduser" method="POST" action="'.$app_http.'">
			<input type="hidden" name="action" value="insert" />
			<div class="column column-half">
				<label for="user[name_first]">First Name</label>
				<input type="text" name="user[name_first]" />
				<label for="user[name_last]">Last Name</label>
				<input type="text" name="user[name_last]" />
				<label for="user[email]">Email</label>
				<input type="text" name="user[email]" />
			</div>
			<div class="column column-half">
				<label for="user[username]">Username</label>
				<input type="text" name="user[username]" />
				<label for="user[password]">Password</label>
				<input type="password" name="user[password]" />
				<label for="confirmpassword">Confirm Password</label>
				<input type="password" name="confirmpassword" />
			</div>
			<input type="submit" name="submituser" value="Add User" />
		</form>';
?>