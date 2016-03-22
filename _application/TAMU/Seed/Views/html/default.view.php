<div>
	This is the default view.
<?php
if ($globaluser->isLoggedIn()) {
	echo " Welcome, {$globaluser->getProfileValue('username')}.";
} else {
	echo " You can log in, <a href=\"{$app_http}login.php\">here</a>.";
}
?>
</div>