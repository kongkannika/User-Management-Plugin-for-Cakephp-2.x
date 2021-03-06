<?php

App::uses('UserMgmtAppModel', 'Usermgmt.Model');
App::uses('CakeEmail', 'Network/Email');

/**
 * Item Model
 *
 * @property UserGroup $UserGroup
 * @property LoginToken $LoginToken
 * 
 */
class User extends UserMgmtAppModel {

	/**
	 * @var array
	 */
	public $actsAs = array('Containable');

	/**
	 * @var int
	 */
	public $recursive = 0;

	/**
	 * This model belongs to following models
	 *
	 * @var array
	 */
	public $belongsTo = array('Usermgmt.UserGroup');

	/**
	 * This model has following models
	 *
	 * @var array
	 */
	public $hasMany = array(
		'LoginToken' => array(
			'className' => 'Usermgmt.LoginToken',
			'limit' => 1,
			'order' => array('LoginToken.modified' => 'DESC')
		),
	);

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
	function LoginValidate() {
		$validate1 = array(
			'email' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter email or username'),
			),
			'password' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter password')
			)
		);
		$this->validate = $validate1;
		return $this->validates();
	}

	/**
	 * model validation array
	 *
	 * @var array
	 * @return bool
	 */
	function RegisterValidate() {
		$validate1 = array(
			'user_group_id' => array(
				'rule' => array('comparison', '!=', 0),
				'message' => 'Please select group'),
			'username' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter username',
					'last' => true),
				'mustUnique' => array(
					'rule' => 'isUnique',
					'message' => 'This username already taken',
					'last' => true),
				'mustBeLonger' => array(
					'rule' => array('minLength', 4),
					'message' => 'Username must be greater than 3 characters',
					'last' => true),
			),
			'first_name' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter first name')
			),
			'last_name' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'on' => 'create',
					'message' => 'Please enter last name')
			),
			'email' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter email',
					'last' => true),
				'mustBeEmail' => array(
					'rule' => array('email'),
					'message' => 'Please enter valid email',
					'last' => true),
				'mustUnique' => array(
					'rule' => 'isUnique',
					'message' => 'This email is already registered',
					'last' => true
				)
			),
			'password' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter password',
					'on' => 'create',
					'last' => true),
				'mustBeLonger' => array(
					'rule' => array('minLength', 6),
					'message' => 'Password must be greater than 5 characters',
					'on' => 'create',
					'last' => true),
				'mustMatch' => array(
					'rule' => array('verifies'),
					'on' => 'create',
					'message' => 'Both passwords must match')
			),
			'cpassword' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter password',
					'on' => 'create',
					'last' => true),
			),
			'captcha' => array(
				'mustMatch' => array(
					'rule' => array('recaptchaValidate'),
					'message' => ''),
			)
		);
		$this->validate = $validate1;
		return $this->validates();
	}

	function ChangePasswordValidate() {
		$validate1 = array(
			'oldpassword' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter old password',
					'last' => true),
				'mustMatch' => array(
					'rule' => array('verifyOldPass'),
					'message' => 'Please enter correct old password'),
			),
			'password' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter password',
					//'on' => 'create',
					'last' => true),
				'mustBeLonger' => array(
					'rule' => array('minLength', 6),
					'message' => 'Password must be greater than 5 characters',
					//'on' => 'create',
					'last' => true),
				'mustMatch' => array(
					'rule' => array('verifies'),
					//'on' => 'create',
					'message' => 'Both passwords must match')
			),
			'cpassword' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter password',
					//'on' => 'create',
					'last' => true),
			),
		);
		$this->validate = $validate1;
		return $this->validates();
	}

	function ResetPasswordValidate() {
		$validate1 = array(
			'password' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter password',
					//'on' => 'create',
					'last' => true),
				'mustBeLonger' => array(
					'rule' => array('minLength', 6),
					'message' => 'Password must be greater than 5 characters',
					//'on' => 'create',
					'last' => true),
				'mustMatch' => array(
					'rule' => array('verifies'),
					//'on' => 'create',
					'message' => 'Both passwords must match')
			),
			'cpassword' => array(
				'mustNotEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter password',
					//'on' => 'create',
					'last' => true),
			),
		);
		$this->validate = $validate1;
		return $this->validates();
	}

	/**
	 * Used to validate captcha
	 *
	 * @access public
	 * @return boolean
	 */
	public function recaptchaValidate() {
		App::import("Vendor", "Usermgmt.recaptcha/recaptchalib");
		$recaptcha_challenge_field = (isset($_POST['recaptcha_challenge_field'])) ? $_POST['recaptcha_challenge_field'] : "";
		$recaptcha_response_field = (isset($_POST['recaptcha_response_field'])) ? $_POST['recaptcha_response_field'] : "";
		$resp = recaptcha_check_answer(PRIVATE_KEY_FROM_RECAPTCHA, $_SERVER['REMOTE_ADDR'], $recaptcha_challenge_field, $recaptcha_response_field);
		$error = $resp->error;
		if (!$resp->is_valid) {
			$this->validationErrors['captcha'][0] = $error;
		}
		return true;
	}

	/**
	 * Used to match passwords
	 *
	 * @access public
	 * @return boolean
	 */
	public function verifies() {
		return ($this->data['User']['password'] === $this->data['User']['cpassword']);
	}

	/**
	 * Used to match old password
	 *
	 * @access public
	 * @return boolean
	 */
	public function verifyOldPass() {
		$userId = $this->userAuth->getUserId();
		$user = $this->findById($userId);
		$oldPass = $this->userAuth->makePassword($this->data['User']['oldpassword'], $user['User']['salt']);
		return ($user['User']['password'] === $oldPass);
	}

	/**
	 * Used to send registration mail to user
	 *
	 * @access public
	 * @param array $user user detail array
	 * @return void
	 */
	public function sendRegistrationMail($user) {
		// send email to newly created user
		$userId = $user['User']['id'];
		$fromConfig = EMAIL_FROM_ADDRESS;
		$fromNameConfig = EMAIL_FROM_NAME;

		$email = new CakeEmail();
		$email->template('Usermgmt.register', 'default');
		$email->emailFormat('html');
		$email->from(array($fromConfig => $fromNameConfig));
		$email->sender(array($fromConfig => $fromNameConfig));
		$email->to($user['User']['email']);
		$email->subject(__('Create Your Account'));
		$email->viewVars(array(
			'name' => $user['User']['first_name']
		));
		//$email->transport('Debug');
		try {
			$result = $email->send();
		}
		catch (Exception $ex) {
			// we could not send the email, ignore it
			$result = "Could not send registration email to userid-" . $userId;
		}
		$this->log($result, LOG_DEBUG);
	}

	/**
	 * Used to send email verification mail to user
	 *
	 * @access public
	 * @param array $user user detail array
	 * @return void
	 */
	public function sendVerificationMail($user) {
		$userId = $user['User']['id'];

		$fromConfig = EMAIL_FROM_ADDRESS;
		$fromNameConfig = EMAIL_FROM_NAME;
		$activateKey = $this->getActivationKey($user['User']['password']);
		$link = Router::url('/userVerification' . "?ident=$userId&activate=$activateKey", true);

		$email = new CakeEmail();
		$email->template('Usermgmt.verify_registration', 'default');
		$email->emailFormat('html');
		$email->from(array($fromConfig => $fromNameConfig));
		$email->sender(array($fromConfig => $fromNameConfig));
		$email->to($user['User']['email']);
		$email->subject(__('Verify Your Registration'));
		$email->viewVars(array(
			'name' => $user['User']['first_name'],
			'link' => $link
		));
		try {
			$result = $email->send();
		}
		catch (Exception $ex) {
			// we could not send the email, ignore it
			$result = 'Could not send verification email to userid-' . $userId;
		}
		$this->log($result, LOG_DEBUG);
	}

	/**
	 * Used to generate activation key
	 *
	 * @access public
	 * @param string $password user password
	 * @return hash
	 */
	public function getActivationKey($password) {
		$salt = Configure::read("Security.salt");
		return md5(md5($password) . $salt);
	}

	/**
	 * Used to send forgot password mail to user
	 *
	 * @access public
	 * @param array $user user detail
	 * @return void
	 */
	public function forgotPassword($user) {
		$userId = $user['User']['id'];

		$fromConfig = EMAIL_FROM_ADDRESS;
		$fromNameConfig = EMAIL_FROM_NAME;
		$activateKey = $this->getActivationKey($user['User']['password']);
		$link = Router::url('/activatePassword' . "?ident=$userId&activate=$activateKey", true);

		$email = new CakeEmail();
		$email->template('Usermgmt.forgot_password', 'default');
		$email->emailFormat('html');
		$email->from(array($fromConfig => $fromNameConfig));
		$email->sender(array($fromConfig => $fromNameConfig));
		$email->to($user['User']['email']);
		$email->subject(__('Request to Reset Your Password'));
		$email->viewVars(array(
			'name' => $user['User']['first_name'],
			'link' => $link
		));
		try {
			$result = $email->send();
		}
		catch (Exception $ex) {
			// we could not send the email, ignore it
			$result = 'Could not send forgot password email to userid-' . $userId;
			debug($link);
			exit;
		}
		$this->log($result, LOG_DEBUG);
	}

	/**
	 * Used to mark cookie used
	 *
	 * @access public
	 * @param string $type
	 * @param array|string $credentials
	 * @return array
	 */
	public function authsomeLogin($type, $credentials = array()) {
		switch ($type) {
			case 'guest':
				// You can return any non-null value here, if you don't
				// have a guest account, just return an empty array
				return array();
			case 'cookie':
				$loginToken = false;
				if (strpos($credentials['token'], ":") !== false) {
					list($token, $userId) = explode(':', $credentials['token']);
					$duration = $credentials['duration'];

					$loginToken = $this->LoginToken->find('first', array(
						'conditions' => array(
							'user_id' => $userId,
							'token' => $token,
							'duration' => $duration,
							'used' => false,
							'expires <=' => date('Y-m-d H:i:s', strtotime($duration)),
						),
						'contain' => false
					));
				}
				if (!$loginToken) {
					return false;
				}
				$loginToken['LoginToken']['used'] = true;
				$this->LoginToken->save($loginToken);

				$conditions = array(
					'User.id' => $loginToken['LoginToken']['user_id']
				);
				break;
			default:
				return array();
		}
		return $this->find('first', compact('conditions'));
	}

	/**
	 * Used to generate cookie token
	 *
	 * @access public
	 * @param integer $userId user id
	 * @param string $duration cookie persist life time
	 * @return string
	 */
	public function authsomePersist($userId, $duration) {
		$token = md5(uniqid(mt_rand(), true));
		$this->LoginToken->create(array(
			'user_id' => $userId,
			'token' => $token,
			'duration' => $duration,
			'expires' => date('Y-m-d H:i:s', strtotime($duration)),
		));
		$this->LoginToken->save();
		return "${token}:${userId}";
	}

	/**
	 * Used to get name by user id
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return string
	 */
	public function getNameById($userId) {
		$res = $this->findById($userId);
		$name = (!empty($res)) ? ($res['User']['first_name'] . ' ' . $res['User']['last_name']) : '';
		return $name;
	}

	/**
	 * Used to check users by group id
	 *
	 * @access public
	 * @param integer $groupId user id
	 * @return boolean
	 */
	public function isUserAssociatedWithGroup($groupId) {
		$res = $this->find('count', array('conditions' => array('User.user_group_id' => $groupId)));
		if (!empty($res)) {
			return true;
		}
		return false;
	}

}
