<?php

App::uses('UserMgmtAppModel', 'Usermgmt.Model');

/**
 * Class UserGroupPermission
 *
 * @property UserGroup $UserGroup
 */
class UserGroupPermission extends UserMgmtAppModel {

	/**
	 * @var int
	 */
	public $recursive = 0;

	/**
	 * @var array
	 */
	public $belongsTo = array('Usermgmt.UserGroup');

}
