<?php
namespace TestFiles\Classes\Data;
use Core\Interfaces as Interfaces;

class TestUser implements Interfaces\User {
	public function logOut() {
		return true;
	}

	public function logIn($username,$password) {
		return true;
	}

	public function isLoggedIn() {
		return true;
	}

	public function getProfileValue($field) {
		return null;
	}

	public function getProfile() {
		return [];
	}

	public function isAdmin() {
		return false;
	}
}
?>
