<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="span12">

			<div class="row">
				<div class="span12">
					<div class="header">
						<?php if(isset($this->logo)) : ?>
						<img src="<?= $this->logo ?>" />
						<?php endif; ?>
					</div>
				</div>
			</div>

			<hr />

			<?php echo $content; ?>
		</div><!-- span12 -->
	</div> <!-- row -->

	<hr />

	<footer>
		<p>
			Copyright &copy; <?php echo date('Y'); ?> by <?= Yii::app()->getParam('companyName'); ?>. |
			All Rights Reserved. |
			<?php echo Yii::powered(); ?>
		</p>
	</footer>
</div>
</body>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->theme->baseUrl . '/css/bootstrap.css'); ?>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->theme->baseUrl . '/css/bootstrap_print.css'); ?>
</html>
