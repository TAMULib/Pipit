<?php
namespace App\Classes\Data;
use TAMU\Core\Classes\Data as CoreData;

/** 
*	Repo for managing application Users
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class Users extends CoreData\AbstractDataBaseRepository {
	public function __construct() {
		parent::__construct('users','id','name_last',array('username','email','name_first','name_last','isadmin','inactive'));
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
		$sql = "SELECT * FROM {$this->primaryTable} u
				LEFT JOIN {$this->primaryTable}_ldap lu ON lu.userid=u.id ";
		$conj = "WHERE";
		$bindparams = array();
		foreach ($data as $field=>$value) {
			$sql .= "{$conj} {$field}=:{$field} ";
			$bindparams[":{$field}"] = $value;
			$conj = "AND";
		}
		$sql .= " ORDER BY name_last";
		if ($result = $this->executeQuery($sql,$bindparams)) {
			return $result;
		}
		return false;
	}

	public function search($term) {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE 
				name_last LIKE ? OR 
				name_first LIKE ? OR
				email LIKE ?
				ORDER BY name_last";
		$bindparams = array("%".$term."%","%".$term."%","%".$term."%");
		if ($result = $this->executeQuery($sql,$bindparams)) {
			return $result;
		}
		return false;
	}

	public function disableById($id) {
		return $this->update($id,array('inactive'=>1));
	}

	public function enableById($id) {
		return $this->update($id,array('inactive'=>0));
	}

	public function add($data) {
		$data['password'] = CoreData\User::hashPassword($data['password']);
		$data['inactive'] = 1;
		return parent::add($data);
	}

	public function update($id,$data) {
		if (!empty($data['password'])) {
			$data['password'] = CoreData\User::hashPassword($data['password']);
		}
		return parent::update($id,$data);
	}

	/**
	*	By default, the seed app only allows for disabling users.
	*	See AbstractDataBaseRepository.php for a functional example of removeById
	*/
	public function removeById($id) {
		return null;
	}
}