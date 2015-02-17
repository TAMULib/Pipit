<?php
$out .= '<form name="login" id="login" method="POST" action="'.$app['path_http'].'">
			<input type="hidden" name="action" value="login" />
			<label for="user[username]">Username</label>
			<input type="text" name="user[username]" />
			<label for="user[password]">Password</label>
			<input type="password" name="user[password]" />
			<input type="submit" name="submitLogin" value="Log In" />
		</form>';
?>