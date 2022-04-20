<?php
namespace TestFiles\Classes\Data;

class TestAdminUser extends TestUser {
	public function isAdmin() {
		return true;
	}
}
?>