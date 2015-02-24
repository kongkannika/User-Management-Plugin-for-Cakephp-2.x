<?php

App::uses('UserMgmtAppModel', 'Usermgmt.Model');

/**
 * LoginToken Model
 *
 * @property User $User
 */
class LoginToken extends UserMgmtAppModel {

	/**
	 * @var int
	 */
	public $recursive = -1;

	/**
	 * This model has following relation with User's model
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'Usermgmt.User',
			'foreignKey' => 'user_id',
		)
	);
}
