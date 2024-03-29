<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<?php $this->renderPartial('//parts/_js'); ?>
<?php

if(isset($this->targetElementSelector)) {
	$cs = Yii::app()->getClientScript();
	$cs->registerScript('appinit', "
		window.addEvent('sandboxReady', function(sandbox) {
			sandbox.addWidget(App." . $this->JsClassName . ", '{$this->targetElementSelector}', {});
		});",
		$cs->coreScriptPosition
	);
}

?>
</head>
<body>
<?php echo $content; ?>
</body>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->theme->baseUrl . '/css/bootstrap.css'); ?>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->theme->baseUrl . '/css/bootstrap_test.css'); ?>
</html>
