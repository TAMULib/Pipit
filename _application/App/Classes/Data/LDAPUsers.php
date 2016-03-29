<?php
namespace App\Classes\Data;
use TAMU\Core\Classes\Data as CoreData;
use TAMU\Core\Utilities as Utilities;

/** 
*	Repo for managing application Users syncable with LDAP data
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class LDAPUsers extends Users {
	private $ldapBaseDn;
	private $ldapUserMap;
	private $ldapSearchAttributes;
	private $ldapSearchFilter;

	public function __construct($ldapUserMap,$ldapSearchAttributes) {
		$this->ldapBaseDn = $GLOBALS['config']['LDAP_BASE_DN'];
		$this->ldapUserMap = $ldapUserMap;
		$this->ldapSearchAttributes = $ldapSearchAttributes;
		$this->ldapSearchFilter = $GLOBALS['config']['LDAP_SEARCH_FILTER'];
		$this->ldapInactiveUserKey = $GLOBALS['config']['LDAP_INACTIVE_USER_KEY'];
		$this->ldapUserNameKey = $GLOBALS['config']['LDAP_USERNAME_KEY'];
		parent::__construct();
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

	public function syncWithLdap() {
		$ldapWrap = new Utilities\LDAPConnector();
		$results = array();
		if ($ldapHandle = $ldapWrap->getConnection()) {
			$ldapUserMap = $this->ldapUserMap;
			if ($usersSearch = ldap_search($ldapHandle, $this->ldapBaseDn,$this->ldapSearchFilter,$this->ldapSearchAttributes)) {
				$userCount = ldap_count_entries($ldapHandle, $usersSearch);
				if ($userCount > 0) {
					$newUsers = array();
					$tempUser = null;
					$ldapUsers = ldap_get_entries($ldapHandle,$usersSearch);
					$x = 0;
					foreach ($ldapUsers as $ldapUser) {
						if (!empty($ldapUser['givenname'][0]) && !empty($ldapUser['sn'][0]) && !empty($ldapUser['mail'][0]) && !empty($ldapUser[$this->ldapUserNameKey][0])) {
							$tempUser = $this->findUserByLDAPName($ldapUser[$this->ldapUserNameKey][0]);
							if ($tempUser) {
								if ($tempUser['inactive'] == 0 && (!empty($ldapUser[$this->ldapInactiveUserKey][0]) && $ldapUser[$this->ldapInactiveUserKey][0] === 'Inactive')) {
									if ($this->updateUser($tempUser['id'],array('inactive'=>1))) {
										$results[] = "Deactivated User with ID {$tempUser['id']}";
									} else {
										$results[] = "Error deactivating User with ID {$tempUser['id']}";
									}			
								}
								$tempUser = null;
							//todo: if the username already exists, but user isnt linked to ldap record, update user with ldap info
							} elseif (!$this->searchAdvanced(array("username"=>$ldapUser[$this->ldapUserNameKey][0]))) {								
								if (empty($ldapUser[$this->ldapInactiveUserKey][0]) || $ldapUser[$this->ldapInactiveUserKey][0] !== 'Inactive') {
									foreach ($ldapUserMap as $oField=>$lField) {
										$newUsers[$x]['core'][$oField] = $ldapUser[$lField][0];
									}
									$newUsers[$x]['core']['inactive'] = 1;
									$newUsers[$x]['ldap'][$this->ldapUserNameKey] = $ldapUser[$this->ldapUserNameKey][0];
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
			$results[] = 'Error connecting to LDAP server';
		}
		$results[] = 'Sync complete';
		return $results;
	}

	public function findUserByLDAPName($accountname) {
		return $this->searchAdvanced(array($this->ldapUserNameKey=>$accountname))[0];
	}

}