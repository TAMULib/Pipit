<div>
	This is the default view.
<?php
if ($globalUser->isLoggedIn()) {
	echo " Welcome, {$globalUser->getProfileValue('username')}.";
} else {
	echo " You can log in, <a href=\"{$app_http}user.php\">here</a>.";
}
?>
</div>