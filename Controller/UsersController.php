<?php

App::uses('UserMgmtAppController', 'Usermgmt.Controller');

/**
 * Class UsersController
 *
 * @property User $User
 * @property UserGroup $UserGroup
 * @property LoginToken $LoginToken
 * @property PaginatorComponent $Paginator
 * @property UserAuthComponent $UserAuth
 */
class UsersController extends UserMgmtAppController {

	/**
	 * This controller uses following models
	 *
	 * @var array
	 */
	public $uses = array('Usermgmt.User', 'Usermgmt.UserGroup', 'Usermgmt.LoginToken');

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');

	/**
	 * Called before the controller action.  You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->User->userAuth = $this->UserAuth;
	}

	/**
	 * Used to display all users by Admin
	 *
	 * @access public
	 * @return array
	 */
	public function index() {
		$this->User->recursive = 0;
		$q = $this->request->query('q');
		$conditions = array(//'User.user_group_id' => 2
		);
		if (!empty($q)) {
			$queries = explode(' ', $q);
			foreach ($queries as $query) {
				$conditions[] = array('OR' => array(
					'User.first_name LIKE' => '%' . $query . '%',
					'User.last_name LIKE' => '%' . $query . '%',
					'User.username LIKE' => '%' . $query . '%',
					'User.email LIKE' => '%' . $query . '%',
					'UserGroup.name LIKE' => '%' . $query . '%',
				));
			}
		}
		$this->Paginator->settings = array(
			'recursive' => 1,
			'conditions' => $conditions
		);
		$users = $this->Paginator->paginate();
		$this->set(compact('users', 'q'));
	}

	/**
	 * Used to display detail of user by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return array
	 */
	public function viewUser($userId = null) {
		if (!empty($userId)) {
			$user = $this->User->find('first', array(
				'contain' => array('UserGroup'),
				'conditions' => array('User.id' => $userId)
			));
			$this->set('user', $user);
		} else {
			$this->redirect('/allUsers');
		}
	}

	/**
	 * Used to display detail of user by user
	 *
	 * @access public
	 * @return array
	 */
	public function profile() {
		$userId = $this->UserAuth->getUserId();
		$this->User->recursive = 0;
		$user = $this->User->read(null, $userId);
		$this->set('user', $user);
	}

	/**
	 * Used to logged in the site
	 *
	 * @access public
	 * @return void
	 */
	public function login() {
		if ($this->request->is('post')) {
			$this->User->set($this->request->data);
			if ($this->User->LoginValidate()) {
				$email = $this->request->data('User.email');
				$password = $this->request->data('User.password');

				$this->User->recursive = 0;
				$user = $this->User->findByUsername($email);
				if (empty($user)) {
					$user = $this->User->findByEmail($email);
					if (empty($user)) {
						$this->Session->setFlash(__('Incorrect Email/Username or Password'));
						return;
					}
				}
				// check for inactive account
				if ($user['User']['id'] != 1 and $user['User']['active'] == 0) {
					$this->Session->setFlash(__('Sorry your account is not active, please contact to Administrator'));
					return;
				}
				// check for verified account
				if ($user['User']['id'] != 1 and $user['User']['email_verified'] == 0) {
					$this->Session->setFlash(__('Your registration has not been confirmed please verify your email or contact to Administrator'));
					return;
				}
				if (empty($user['User']['salt'])) {
					$hashed = md5($password);
				} else {
					$hashed = $this->UserAuth->makePassword($password, $user['User']['salt']);
				}
				if ($user['User']['password'] === $hashed) {
					if (empty($user['User']['salt'])) {
						$salt = $this->UserAuth->makeSalt();
						$user['User']['salt'] = $salt;
						$user['User']['password'] = $this->UserAuth->makePassword($password, $salt);
						$this->User->save($user, false);
					}
					$this->UserAuth->login($user);
					$remember = $this->request->data('User.remember');
					if ($remember) {
						$this->UserAuth->persist('2 weeks');
					}
					$OriginAfterLogin = $this->Session->read('Usermgmt.OriginAfterLogin');
					$this->Session->delete('Usermgmt.OriginAfterLogin');
					$redirect = LOGIN_REDIRECT_URL;
					if (!empty($OriginAfterLogin)) {
						$redirect = $OriginAfterLogin;
					} elseif ($user['User']['user_group_id'] == 1) {
						$redirect = ADMIN_LOGIN_REDIRECT_URL;
					}
					$this->redirect($redirect);
				} else {
					$this->Session->setFlash(__('Incorrect Email/Username or Password'));
					return;
				}
			}
		}
	}

	/**
	 * Used to logged out from the site
	 *
	 * @access public
	 * @return void
	 */
	public function logout() {
		$this->UserAuth->logout();
		$this->Session->setFlash(__('You are successfully signed out'), 'default', array('class' => 'message success'));
		$this->redirect(LOGOUT_REDIRECT_URL);
	}

	/**
	 * Used to register on the site
	 *
	 * @access public
	 * @return void
	 */
	public function register() {
		$userId = $this->UserAuth->getUserId();
		if ($userId) {
			$this->redirect('/dashboard');
		}
		if (SITE_REGISTRATION) {
			$userGroups = $this->UserGroup->getGroupsForRegistration();
			$this->set('userGroups', $userGroups);
			if ($this->request->is('post')) {
				if (USE_RECAPTCHA && !$this->request->is('ajax')) {
					$this->request->data['User']['captcha'] = (isset($this->request->data['recaptcha_response_field'])) ? $this->request->data['recaptcha_response_field'] : "";
				}
				$this->User->set($this->request->data);
				if ($this->User->RegisterValidate()) {
					if (!isset($this->request->data['User']['user_group_id'])) {
						$this->request->data['User']['user_group_id'] = DEFAULT_GROUP_ID;
					} elseif (!$this->UserGroup->isAllowedForRegistration($this->request->data['User']['user_group_id'])) {
						$this->Session->setFlash(__('Please select correct register as'));
						return;
					}
					$this->request->data['User']['active'] = 1;
					if (!EMAIL_VERIFICATION) {
						$this->request->data['User']['email_verified'] = 1;
					}
					$ip = '';
					if (isset($_SERVER['REMOTE_ADDR'])) {
						$ip = $_SERVER['REMOTE_ADDR'];
					}
					$this->request->data['User']['ip_address'] = $ip;
					$salt = $this->UserAuth->makeSalt();
					$this->request->data['User']['salt'] = $salt;
					$this->request->data['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password'], $salt);
					$this->User->save($this->request->data, false);
					$userId = $this->User->getLastInsertID();
					$this->User->recursive = 0;
					$user = $this->User->findById($userId);
					if (SEND_REGISTRATION_MAIL && !EMAIL_VERIFICATION) {
						$this->User->sendRegistrationMail($user);
					}
					if (EMAIL_VERIFICATION) {
						$this->User->sendVerificationMail($user);
					}
					if (isset($this->request->data['User']['email_verified']) && $this->request->data['User']['email_verified']) {
						$this->UserAuth->login($user);
						$this->redirect('/');
					} else {
						$this->Session->setFlash(__('Please check your mail and confirm your registration'));
						$this->redirect('/register');
					}
				}
			}
		} else {
			$this->Session->setFlash(__('Sorry new registration is currently disabled, please try again later'));
			$this->redirect('/login');
		}
	}

	/**
	 * Used to change the password by user
	 *
	 * @access public
	 * @return void
	 */
	public function changePassword() {
		$userId = $this->UserAuth->getUserId();
		if ($this->request->is('post')) {
			$this->User->set($this->request->data);
			if ($this->User->ChangePasswordValidate()) {
				$user = array();
				$user['User']['id'] = $userId;
				$salt = $this->UserAuth->makeSalt();
				$user['User']['salt'] = $salt;
				$user['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password'], $salt);
				$this->User->save($user, false);
				$this->LoginToken->deleteAll(array('LoginToken.user_id' => $userId), false);
				$this->Session->setFlash(__('Password changed successfully'), 'default', array('class' => 'message success'));
				$this->redirect('/profile');
			}
		}
	}

	/**
	 * Used to change the user password by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function changeUserPassword($userId = null) {
		if (!empty($userId)) {
			$name = $this->User->getNameById($userId);
			$this->set('name', $name);
			if ($this->request->is('post')) {
				$this->User->set($this->request->data);
				if ($this->User->RegisterValidate()) {
					$user = array();
					$user['User']['id'] = $userId;
					$salt = $this->UserAuth->makeSalt();
					$user['User']['salt'] = $salt;
					$user['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password'], $salt);
					$this->User->save($user, false);
					$this->LoginToken->deleteAll(array('LoginToken.user_id' => $userId), false);
					$this->Session->setFlash(__('Password for %s changed successfully', $name), 'default', array('class' => 'message success'));
					$this->redirect('/allUsers');
				}
			}
		} else {
			$this->redirect('/allUsers');
		}
	}

	/**
	 * Used to add user on the site by Admin
	 *
	 * @access public
	 * @return void
	 */
	public function addUser() {
		$userGroups = $this->UserGroup->getGroups();
		$this->set('userGroups', $userGroups);
		if ($this->request->is('post')) {
			$this->User->set($this->request->data);
			if ($this->User->RegisterValidate()) {
				$this->request->data['User']['email_verified'] = 1;
				$this->request->data['User']['active'] = 1;
				$salt = $this->UserAuth->makeSalt();
				$this->request->data['User']['salt'] = $salt;
				$this->request->data['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password'], $salt);
				$this->User->save($this->request->data, false);
				$this->Session->setFlash(__('The user has been saved.'), 'default', array('class' => 'message success'));
				$this->redirect(array('controller' => 'users', 'action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

	/**
	 * Used to edit user on the site by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function editUser($userId = null) {
		if (!empty($userId)) {
			$userGroups = $this->UserGroup->getGroups();
			$this->set('userGroups', $userGroups);
			if ($this->request->is(array('put', 'post'))) {
				$this->User->id = $userId;
				$this->User->set($this->request->data);
				if ($this->User->RegisterValidate()) {
					$this->User->save($this->request->data, false);
					$this->Session->setFlash(__('The user is successfully updated'), 'default', array('class' => 'message success'));
					$this->redirect('/allUsers');
				} else {
					$this->Session->setFlash(__('The user could not be updated. Please try again'));
				}
			} else {
				$user = $this->User->read(null, $userId);
				$this->request->data = null;
				if (!empty($user)) {
					$user['User']['password'] = '';
					$this->request->data = $user;
				}
			}
		} else {
			$this->redirect('/allUsers');
		}
	}

	/**
	 * Used to delete the user by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function deleteUser($userId = null) {
		if (!empty($userId)) {
			if ($this->request->is('post')) {
				if ($this->User->delete($userId, false)) {
					$this->LoginToken->deleteAll(array('LoginToken.user_id' => $userId), false);
					$this->Session->setFlash(__('User is successfully deleted'), 'default', array('class' => 'message success'));
				}
			}
		}
		$this->redirect('/allUsers');
	}

	/**
	 * Used to show dashboard of the user
	 *
	 * @access public
	 * @return array
	 */
	public function dashboard() {
		$userId = $this->UserAuth->getUserId();
		$this->User->recursive = 0;
		$user = $this->User->findById($userId);
		$this->set('user', $user);
	}

	/**
	 * Used to activate or deactivate user by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @param integer $active active or inactive
	 * @return void
	 */
	public function makeActiveInactive($userId = null, $active = 0) {
		if (!empty($userId)) {
			$user = array();
			$user['User']['id'] = $userId;
			$user['User']['active'] = ($active) ? 1 : 0;
			$this->User->save($user, false);
			if ($active) {
				$this->Session->setFlash(__('User is successfully activated'), 'default', array('class' => 'message success'));
			} else {
				$this->Session->setFlash(__('User is successfully deactivated'), 'default', array('class' => 'message success'));
			}
		}
		$this->redirect('/allUsers');
	}

	/**
	 * Used to verify email of user by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function verifyEmail($userId = null) {
		if (!empty($userId)) {
			$user = array();
			$user['User']['id'] = $userId;
			$user['User']['email_verified'] = 1;
			$this->User->save($user, false);
			$this->Session->setFlash(__('User email is successfully verified'), 'default', array('class' => 'message success'));
		}
		$this->redirect('/allUsers');
	}

	/**
	 * Used to show access denied page if user want to view the page without permission
	 *
	 * @access public
	 * @return void
	 */
	public function accessDenied() {

	}

	/**
	 * Used to verify user's email address
	 *
	 * @access public
	 * @return void
	 */
	public function userVerification() {
		if (isset($this->request->query['ident']) && isset($this->request->query['activate'])) {
			$userId = $this->request->query['ident'];
			$activateKey = $this->request->query['activate'];
			$user = $this->User->read(null, $userId);
			if (!empty($user)) {
				if (!$user['User']['email_verified']) {
					$password = $user['User']['password'];
					$theKey = $this->User->getActivationKey($password);
					if ($activateKey == $theKey) {
						$user['User']['email_verified'] = 1;
						$this->User->save($user, false);
						if (SEND_REGISTRATION_MAIL && EMAIL_VERIFICATION) {
							$this->User->sendRegistrationMail($user);
						}
						$this->Session->setFlash(__('Thank you, your account is activated now'), 'default', array('class' => 'message success'));
					}
				} else {
					$this->Session->setFlash(__('Thank you, your account is already activated'), 'default', array('class' => 'message success'));
				}
			} else {
				$this->Session->setFlash(__('Sorry something went wrong, please click on the link again'));
			}
		} else {
			$this->Session->setFlash(__('Sorry something went wrong, please click on the link again'));
		}
		$this->redirect('/login');
	}

	/**
	 * Used to send forgot password email to user
	 *
	 * @access public
	 * @return void
	 */
	public function forgotPassword() {
		if ($this->request->is('post')) {
			$this->User->set($this->request->data);
			if ($this->User->LoginValidate()) {
				$email = $this->request->data('User.email');
				$this->User->recursive = 0;
				$user = $this->User->findByUsername($email);
				if (empty($user)) {
					$user = $this->User->findByEmail($email);
					if (empty($user)) {
						$this->Session->setFlash(__('Incorrect Email/Username'));
						return;
					}
				}
				// check for inactive account
				if ($user['User']['id'] != 1 and $user['User']['email_verified'] == 0) {
					$this->Session->setFlash(__('Your registration has not been confirmed yet please verify your email before reset password'));
					return;
				}
				$this->User->forgotPassword($user);
				$this->Session->setFlash(__('Please check your mail for reset your password'), 'default', array('class' => 'message success'));
				$this->redirect('/login');
			}
		}
	}

	/**
	 *  Used to reset password when user comes on the by clicking the password reset link from their email.
	 *
	 * @access public
	 * @return void
	 */
	public function activatePassword() {
		if ($this->request->is('post')) {
			$userId = $this->request->data('User.ident');
			$activateKey = $this->request->data('User.activate');

			if ($userId && $activateKey) {
				$this->set('ident', $userId);
				$this->set('activate', $activateKey);

				$this->User->set($this->request->data);
				if ($this->User->ResetPasswordValidate()) {
					$user = $this->User->read(null, $userId);

					if (!empty($user)) {
						$password = $user['User']['password'];
						$theKey = $this->User->getActivationKey($password);

						if ($theKey == $activateKey) {
							$newPassword = $this->request->data['User']['password'];
							$salt = $this->UserAuth->makeSalt();

							$user['User']['salt'] = $salt;
							$user['User']['password'] = $this->UserAuth->makePassword($newPassword, $salt);

							$this->User->save($user, false);
							$this->Session->setFlash(__('Your password has been reset successfully'), 'default', array('class' => 'message success'));
							$this->redirect('/login');
						} else {
							$this->Session->setFlash(__('Something went wrong, please send password reset link again'));
						}
					} else {
						$this->Session->setFlash(__('Something went wrong, please click again on the link in email'));
					}
				}
			} else {
				$this->Session->setFlash(__('Something went wrong, please click again on the link in email'));
			}
		} else {
			if (isset($this->request->query['ident']) && isset($this->request->query['activate'])) {
				$this->set('ident', $this->request->query['ident']);
				$this->set('activate', $this->request->query['activate']);
			}
		}
	}

	/**
	 * Used to send email verification mail to user
	 *
	 * @access public
	 * @return void
	 */
	public function emailVerification() {
		if ($this->request->is('post')) {
			$this->User->set($this->request->data);
			if ($this->User->LoginValidate()) {
				$email = $this->request->data['User']['email'];
				$this->User->recursive = 0;
				$user = $this->User->findByUsername($email);
				if (empty($user)) {
					$user = $this->User->findByEmail($email);
					if (empty($user)) {
						$this->Session->setFlash(__('Incorrect Email/Username'));
						return;
					}
				}
				if ($user['User']['email_verified'] == 0) {
					$this->User->sendVerificationMail($user);
					$this->Session->setFlash(__('Please check your mail to verify your email'));
				} else {
					$this->Session->setFlash(__('Your email is already verified'), 'default', array('class' => 'message success'));
				}
				$this->redirect('/login');
			}
		}
	}

}
