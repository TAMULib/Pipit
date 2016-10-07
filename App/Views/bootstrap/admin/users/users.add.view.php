<?php
echo '<form class="do-submit" name="adduser" method="POST" action="'.$app_http.'">
			<input type="hidden" name="action" value="insert" />
			<div class="col col-sm-6">
				<div class="form-group">
					<label for="user[name_first]">First Name</label>
					<input class="form-control" type="text" name="user[name_first]" />
				</div>
				<div class="form-group">
					<label for="user[name_last]">Last Name</label>
					<input class="form-control" type="text" name="user[name_last]" />
				</div>
				<div class="form-group">
					<label for="user[email]">Email</label>
					<input class="form-control" type="text" name="user[email]" />
				</div>
			</div>
			<div class="col col-sm-6">
				<div class="form-group">
					<label for="user[username]">Username</label>
					<input class="form-control" type="text" name="user[username]" />
				</div>
				<div class="form-group">
					<label for="user[password]">Password</label>
					<input class="form-control" type="password" name="user[password]" />
				</div>
				<div class="form-group">
					<label for="confirmpassword">Confirm Password</label>
					<input class="form-control" type="password" name="confirmpassword" />
				</div>
			</div>
			<input class="btn btn-default" type="submit" name="submituser" value="Add User" />
		</form>';
?>