<?php $this->beginContent('//layouts/main'); ?>
<div class="container">
	<div class="row">
		<div class="span3">
			<div class="well sidebar-nav">
				<?php $this->widget('bootstrap.widgets.BootMenu', array(
					'type'=>'list', // '', 'tabs', 'pills' (or 'list')
					'stacked'=>false, // whether this is a stacked menu
					'items'=>$this->menu,
				)); ?>
			</div>
		</div>

		<div class="span9">

			<?php if(isset($this->breadcrumbs)):?>
				<?php $this->widget('bootstrap.widgets.BootBreadcrumbs', array(
					'links'=>$this->breadcrumbs
				)); ?>
			<?php endif?>
			<?php echo $content; ?>
		</div><!-- span9 -->
		
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
