<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{

	protected $_id;
	private $_user;

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		// Try to authenticate one of our vendors.
// 		$user = $this->loadUser();

// 		if(!empty($user)) {

// 			// If the user is marked as dump, it's kinda like they don't exist.
// 			if($user->status == User::STATUS_DUMP) {
// 				$this->errorCode = self::ERROR_DELETED_USER;
// 			}
// 			else {
// 				$this->_id = $user->id;
// 				$this->errorCode=self::ERROR_NONE;
// 			}

// 			return !$this->errorCode;
// 		}

// 		$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
// 		return $this->errorCode;

		echo '<pre>' . CVarDumper::dumpAsString($this) . '</pre>';

		// Otherwise try to authenticate temp admin user.

				$users=array(
					// username => password
					'demo'=>'demo',
					'admin'=>'admin',
				);

				if(!isset($users[$this->username]))
					$this->errorCode=self::ERROR_USERNAME_INVALID;
				else if($users[$this->username]!==$this->password)
					$this->errorCode=self::ERROR_PASSWORD_INVALID;
				else
					$this->errorCode=self::ERROR_NONE;
				return !$this->errorCode;
	}

	protected function loadUser() {

		if (!empty($this->_user)) {
			return $this->_user;
		}

		/* @var $user User */
		$user = User::model()->find('email_address=:email', array(
			':email'=>$this->username));

		if (!empty($user) && ($user->password === $user->calculateHash($this->password, $user->salt)))
		{
			$this->_id = $user->id;
			return $user;
		}

		return null;
	}

	public function setUser($user) {
		$this->_user = $user;
	}

	public function getId() {
		return $this->_id;
	}
}