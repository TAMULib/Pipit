<?php
namespace TAMU\Seed\Classes\Data;
use TAMU\Seed\Interfaces as Interfaces;

/** 
*	Repo for managing application Users
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class Users extends DBObject implements Interfaces\DataBaseRepository {

	public function __construct() {
		$this->primaryTable = 'users';
		parent::__construct();
	}

	public function get() {
		$sql = "SELECT * FROM `{$this->primaryTable}` ORDER BY `name_last`";
		return $this->queryWithIndex($sql,"id");
	}

/**	@todo Generalize and possibly move to separate utility */

	public function syncWithLdap() {
		$ldapWrap = new ldap($GLOBALS['config']['ldap']['url'],$GLOBALS['config']['ldap']['port'],$GLOBALS['config']['ldap']['user'],$GLOBALS['config']['ldap']['password']);
		$results = array();
		if ($ldapHandle = $ldapWrap->getConnection()) {
			$ldapUserMap = array("name_first"=>"givenname","name_last"=>"sn","email"=>"mail","username"=>"samaccountname");
			if ($usersSearch = ldap_search($ldapHandle, "OU=UserAccounts,DC=library,DC=tamu,DC=edu","(|(samaccountname=*))",array("samaccountname","givenname","sn","mail","edupersonaffiliation"))) {
				$userCount = ldap_count_entries($ldapHandle, $usersSearch);
				if ($userCount > 0) {
					$newUsers = array();
					$tempUser = null;
					$ldapUsers = ldap_get_entries($ldapHandle,$usersSearch);
					$x = 0;
					foreach ($ldapUsers as $ldapUser) {
						if (!empty($ldapUser['givenname'][0]) && !empty($ldapUser['sn'][0]) && !empty($ldapUser['mail'][0]) && !empty($ldapUser['samaccountname'][0])) {
							$tempUser = $this->findUserByLDAPName($ldapUser['samaccountname'][0]);
							if ($tempUser) {
								if ($tempUser['inactive'] == 0 && (!empty($ldapUser['edupersonaffiliation'][0]) && $ldapUser['edupersonaffiliation'][0] === 'Inactive')) {
									if ($this->updateUser($tempUser['id'],array('inactive'=>1))) {
										$results[] = "Deactivated User with ID {$tempUser['id']}";
									} else {
										$results[] = "Error deactivating User with ID {$tempUser['id']}";
									}			
								}
								$tempUser = null;
							//todo: if the username already exists, but user isnt linked to ldap record, update user with ldap info
							} elseif (!$this->searchUsersAdvanced(array("username"=>$ldapUser['samaccountname'][0]))) {								
								if (empty($ldapUser['edupersonaffiliation'][0]) || $ldapUser['edupersonaffiliation'][0] !== 'Inactive') {
									foreach ($ldapUserMap as $oField=>$lField) {
										$newUsers[$x]['core'][$oField] = $ldapUser[$lField][0];
									}	
									$newUsers[$x]['ldap']['samaccountname'] = $ldapUser['samaccountname'][0];
								}
							}
						}
						$x++; 
					}
					$newUserCount = count($newUsers);
					if ($newUserCount > 0) {
						$this->db->handle->beginTransaction();
						$flag = false;
						foreach ($newUsers as $newUser) {
							if ($newUser['ldap']['userid'] = $this->buildInsertStatement($newUser['core'])) {
								if (!$this->buildInsertStatement($newUser['ldap'],"{$this->primaryTable}_ldap")) {
									$flag = true;
									break;
								}
							} else {
								$flag = true;
								break;
							}
						}
						if ($flag) {
							$this->db->handle->rollBack();
							$results[] = "There was an error adding {$newUserCount} new user(s) from LDAP";
						} else {
							$this->db->handle->commit();
							$results[] = "{$newUserCount} new user(s) were added from LDAP";
						}
					}
				}
			}
		} else {
			$results[] = 'Error getting connecting to LDAP server';
		}
		$results[] = 'Sync complete';
		return $results;
	}

	public function findUserByLDAPName($accountname) {
		return $this->searchUsersAdvanced(array("samaccountname"=>$accountname))[0];
	}

	public function searchAdvanced($data) {
		$sql = "SELECT * FROM `{$this->primaryTable}` u
				LEFT JOIN `{$this->primaryTable}_ldap` lu ON lu.userid=u.id ";
		$conj = "WHERE";
		$bindparams = array();
		foreach ($data as $field=>$value) {
			$sql .= "{$conj} `{$field}`=:{$field} ";
			$bindparams[":{$field}"] = $value;
			$conj = "AND";
		}
		$sql .= " ORDER BY `name_last`";
		if ($result = $this->executeQuery($sql,$bindparams)) {
			return $result;
		}
		return false;
	}

	public function search($term) {
		$sql = "SELECT * FROM `{$this->primaryTable}` WHERE 
				`name_last` LIKE ? OR 
				`name_first` LIKE ? OR
				`email` LIKE ?
				ORDER BY `name_last`";
		$bindparams = array("%".$term."%","%".$term."%","%".$term."%");
		if ($result = $this->executeQuery($sql,$bindparams)) {
			return $result;
		}
		return false;
	}

	public function getById($id) {
		$sql = "SELECT * FROM `{$this->primaryTable}` WHERE id=:id";
		$temp = $this->executeQuery($sql,array(":id"=>$id));
		return $temp[0];
	}

	public function add($data) {
		return $this->buildInsertStatement($data);
	}

	public function update($id,$data) {
		return $this->buildUpdateStatement($id,$data);
	}

	public function disableById($id) {
		return $this->update($id,array('inactive'=>1));
	}

	public function enableById($id) {
		return $this->update($id,array('inactive'=>0));
	}

	/**
	*	By default, the seed app only allows for disabling users.
	*	See Widgets.php for a functional example of removeById
	*/
	public function removeById($id) {
		return null;
	}
}