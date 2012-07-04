<?php 

/**
 * Render the global window.yii object into the dom.
 * 
 * <code>
 *   // Add in some custom parameters
 *   Yii::app()->globaljs->addParams(array(
 *   	'controller' => get_class($this),
 *   	'action' => $this->action->id
 *   ));
 *   Yii::app()->globaljs->addUserData();
 *   Yii::app()->globaljs->render();
 * </code>
 */
class LWGlobalJs extends CApplicationComponent {
	
	private $a;
	
	public function __construct() {
		$this->a = new DotArray();
	}
	
	public function addUserData() {
		$this->addParams(array(
			'isGuest' => Yii::app()->user->isGuest,
			'hasLoggedInBefore' => Yii::app()->user->hasLoggedInAtSomePoint()
		));
		if (!Yii::app()->user->isGuest) {
			$this->addParams(Yii::app()->user->getUser()->getPublicInfo());
		}
	}
	
	public function addParams($array) {
		foreach($array as $path => $value) {
			$this->a->set($path, $value);
		}	
	}
	
	public function addParam($path, $value) {
		$this->a->set($path, $value);
	}
	
	public function toString() {
		return sprintf('window.yii = %s', CJSON::encode($this->a->toArray()));
	}
	
	public function render($name = 'helpers', $pos = CClientScript::POS_HEAD) {
		Yii::app()->clientScript->registerScript($name, $this->toString(), $pos);
	}
	
}