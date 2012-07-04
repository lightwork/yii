<?php 

class LWWebApplication extends CWebApplication {
	
	/**
	 * Get a param defined in the config params array.
	 * 
	 * @param string $name
	 * @param mixed $default
	 */
	public function getParam($name, $default = null) {
		if ( isset(Yii::app()->params[$name]) )
			return Yii::app()->params[$name];
		else
			return $default;
	}
}

?>