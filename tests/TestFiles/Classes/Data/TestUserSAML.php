<?php
namespace TestFiles\Classes\Data;
use Pipit\Interfaces as Interfaces;

class TestUserSAML implements Interfaces\User {
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
