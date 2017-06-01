<?php
namespace TestFiles\Classes\Data;

class TestUser {
	public function logOut() {
		return true;
	}

	public function isLoggedIn() {
		return true;
	}

	public function isAdmin() {
		return false;
	}

}
?>