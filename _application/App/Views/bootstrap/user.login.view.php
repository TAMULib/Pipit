<form class="col col-sm-4" name="login" id="login" method="POST" action="<?php echo $app_http;?>">
	<input type="hidden" name="action" value="login" />
	<div class="form-group">
		<label for="user[username]">Username</label>
		<input class="form-control" type="text" name="user[username]" />
	</div>
	<div class="form-group">
		<label for="user[password]">Password</label>
		<input class="form-control" type="password" name="user[password]" />
	</div>
	<input class="btn btn-default" type="submit" name="submitLogin" value="Log In" />
</form>
