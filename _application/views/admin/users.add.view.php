<?php
$out .= '<form class="do-submit" name="adduser" method="POST" action="'.$app_http.'">
			<input type="hidden" name="action" value="insert" />
			<div class="column column-half">
				<label for="user[name_first]">First Name</label>
				<input type="text" name="user[name_first]" />
				<label for="user[name_last]">Last Name</label>
				<input type="text" name="user[name_last]" />
				<label for="user[email]">Email</label>
				<input type="text" name="user[email]" />
			</div>
			<input type="submit" name="submituser" value="Add Owner" />
		</form>';
?>