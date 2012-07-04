<?php $this->beginContent('//layouts/main'); ?>
<div class="container">
	<div class="row">
		<div class="span12">
			<?php if(isset($this->breadcrumbs)):?>
				<?php $this->widget('bootstrap.widgets.BootBreadcrumbs', array(
					'links'=>$this->breadcrumbs
				)); ?>
			<?php endif?>
			<div>
			<?php echo $content; ?>
			</div>
		</div><!-- span12 -->
		
	</div> <!-- row-fluid -->

	<hr />

	<footer>
		<p>
			Copyright &copy; <?php echo date('Y'); ?> by <?= Yii::app()->getParam('companyName'); ?>. | 
			All Rights Reserved. | 
			<?php echo Yii::powered(); ?>
		</p>
	</footer>
</div>
<?php $this->endContent(); ?>
