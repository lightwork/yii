<?php

	// Render all alerts that are looked after by BootAlert
	$this->widget('bootstrap.widgets.BootAlert');

	$bootAlert = YiiBase::createComponent(array('class'=>'bootstrap.widgets.BootAlert'));

	// Render all keys that are not looked after by BootAlert
	foreach(Yii::app()->user->flashes as $key=>$flash) {
		if(!in_array($key, $bootAlert->keys)) {
			$this->renderPartial('//parts/_flash', array(
				'key'=>$key,
				'flash'=>$flash,
			));
		}
	}

?>