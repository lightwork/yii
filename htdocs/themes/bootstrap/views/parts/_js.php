<?php

	$cs = Yii::app()->getClientScript();
	$cs->coreScriptPosition = CClientScript::POS_HEAD;

	$cs->registerCoreScript( 'mootools' );
	$cs->registerCoreScript( 'framework' );

	$themeScripts = array(
		'/js/bootstrap.js',
		'/js/actions/' . $this->JsClassNamePath . '.js',
	);

	$themeBase = Yii::app()->themeManager->baseUrl . '/' . Yii::app()->theme->baseUrl;
	foreach($themeScripts as $script) {
		$cs->registerScriptFile(Yii::app()->theme->baseUrl. $script, CClientScript::POS_END);
	}


?>
