<?php

	// Render all alerts that are looked after by TbAlert
	$this->widget('bootstrap.widgets.TbAlert');

	$bootAlert = YiiBase::createComponent(array('class'=>'bootstrap.widgets.TbAlert'));

	// Render all keys that are not looked after by TbAlert
	foreach(Yii::app()->user->flashes as $key=>$flash) {
		if(!in_array($key, $bootAlert->keys)) {
			$this->renderPartial('//parts/_flash', array(
				'key'=>$key,
				'flash'=>$flash,
			));
		}
	}

?>