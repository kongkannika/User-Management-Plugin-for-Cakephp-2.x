<?php

App::uses('UserMgmtAppModel', 'Usermgmt.Model');
App::uses('CakeEmail', 'Network/Email');

/**
 * Class UserGroup
 *
 * @property UserGroupPermission $UserGroupPermission
 */
class UserGroup extends UserMgmtAppModel {

	/**
	 * @var int
	 */
	public $recursive = -1;

	/**
	 * This model has following models
	 *
	 * @var array
	 */
	public $hasMany = array('Usermgmt.UserGroupPermission');

	/**
	 * model validation array
	 *
	 * @var array
	 */
	public $validate = array();

	/**
	 * model validation array
	 *
	 * @var array
	 * @return bool
	 */
	function addValidate() {
		$validate1 = array(
			'name' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter group name',
					'last' => true),
				'mustUnique' => array(
					'rule' => 'isUnique',
					'message' => 'This group name already added',
					'on' => 'create',
					'last' => true),
			),
			'alias_name' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter alias group name',
					'last' => true),
				'mustUnique' => array(
					'rule' => 'isUnique',
					'message' => 'This alias group name already added',
					'on' => 'create',
					'last' => true),
			),
		);
		$this->validate = $validate1;
		return $this->validates();
	}

	/**
	 * Used to check permissions of group
	 *
	 * @access public
	 * @param string $controller controller name
	 * @param string $action action name
	 * @param integer $userGroupID group id
	 * @return boolean
	 */
	public function isUserGroupAccess($controller, $action, $userGroupID) {
		$includeGuestPermission = false;
		if (!PERMISSIONS) {
			return true;
		}
		if ($userGroupID == ADMIN_GROUP_ID && !ADMIN_PERMISSIONS) {
			return true;
		}

		$permissions = $this->getPermissions($userGroupID, $includeGuestPermission);
		$access = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller))) . '/' . $action;
		if (in_array($access, $permissions)) {
			return true;
		}
		return false;
	}

	/**
	 * Used to check permissions of guest group
	 *
	 * @access public
	 * @param string $controller controller name
	 * @param string $action action name
	 * @return boolean
	 */
	public function isGuestAccess($controller, $action) {
		if (PERMISSIONS) {
			return $this->isUserGroupAccess($controller, $action, GUEST_GROUP_ID);
		}
		else {
			return true;
		}
	}

	/**
	 * Used to get permissions from cache or database of a group
	 *
	 * @access public
	 * @param integer $userGroupID group id
	 * @return array
	 */
	public function getPermissions($userGroupID) {
		$permissions = array();
		// using the cake cache to store rules
		$cacheKey = 'rules_for_group_' . $userGroupID;
		$actions = Cache::read($cacheKey, 'UserMgmt');
		if ($actions === false) {
			$actions = $this->UserGroupPermission->find('all', array('conditions' => 'UserGroupPermission.user_group_id = ' . $userGroupID . ' AND UserGroupPermission.allowed = 1'));
			Cache::write($cacheKey, $actions, 'UserMgmt');
		}
		foreach ($actions as $action) {
			$permissions[] = $action['UserGroupPermission']['controller'] . '/' . $action['UserGroupPermission']['action'];
		}
		return $permissions;
	}

	/**
	 * Used to get group names
	 *
	 * @access public
	 * @return array
	 */
	public function getGroupNames() {
		$result = $this->find('list', array('order' => 'id'));
		return array_values($result);
	}

	/**
	 * Used to get group names with ids
	 *
	 * @access public
	 * @return array
	 */
	public function getGroupNamesAndIds() {
		$result = $this->find('all', array('order' => 'id'));
		$userGroups = array();
		foreach ($result as $row) {
			$data = array();
			$data['id'] = $row['UserGroup']['id'];
			$data['name'] = $row['UserGroup']['name'];
			$data['alias_name'] = $row['UserGroup']['alias_name'];
			$userGroups[] = $data;
		}
		return $userGroups;
	}

	/**
	 * Used to get group names with ids without guest group
	 *
	 * @access public
	 * @return array
	 */
	public function getGroups() {
		$userGroups = $this->find('list', array('conditions' => array('id !=' => 3), 'order' => 'id DESC'));
		return $userGroups;
	}

	/**
	 * Used to get group names with ids for registration
	 *
	 * @access public
	 * @return array
	 */
	public function getGroupsForRegistration() {
		$userGroups = $this->find('list', array('order' => 'id', 'conditions' => array('allowRegistration' => 1)));
		return $userGroups;
	}

	/**
	 * Used to check group is available for registration
	 *
	 * @access public
	 * @param integer $groupId group id
	 * @return boolean
	 */
	function isAllowedForRegistration($groupId) {
		$result = $this->findById($groupId);
		if (!empty($result)) {
			if ($result['UserGroup']['allowRegistration'] == 1) {
				return true;
			}
		}
		return false;
	}

}
