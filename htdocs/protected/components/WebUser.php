<?php

// this file must be stored in:
// protected/components/WebUser.php

class WebUser extends CWebUser {

	// Store model to not repeat query.
	private $_model;

	public function init() {
		parent::init();
	}

	/**
	 *
	 * @return User
	 */
	public function getUser() {
		return $this->loadUser(Yii::app()->user->Id);
	}

	// Load user model.
	protected function loadUser($id=null) {
		if($this->_model===null) {
			if($id!==null) {
				$this->_model=User::model()->findByPk($id);
			}
		}
		return $this->_model;
	}


	/**
	 * Does the user have the `admin` role assigned?
	 * @return boolean
	 */
	public function getIsAdmin() {
		$roles = Yii::app()->authManager->getRoles($this->Id);
		return in_array('admin', array_keys($roles));
	}

	public function dump()
	{
		echo '<pre>' . CVarDumper::dumpAsString($this->user->attributes) . '</pre>';
	}

	public function dumpRoles()
	{
		$arrayAuthRoleItems = Yii::app()->authManager->getAuthItems(2, Yii::app()->user->Id);
		$arrayKeys = array_keys($arrayAuthRoleItems);
		echo '<pre>' . CVarDumper::dumpAsString($arrayKeys) . '</pre>';
	}

}
