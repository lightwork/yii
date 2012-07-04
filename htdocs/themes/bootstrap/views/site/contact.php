<?php
$this->pageTitle=Yii::app()->name . ' - Contact Us';
$this->breadcrumbs=array(
	'Contact',
);
?>

<h1>Contact Us</h1>

<?php if(Yii::app()->user->hasFlash('contact')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('contact'); ?>
</div>

<?php else: ?>

<p>
If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.
</p>

<div class="span6">
<?php /** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id'=>'contact-form',
    'type'=>'horizontal',
)); ?>

 
<fieldset>
 
	 <p class="note">Fields with <span class="required">*</span> are required.</p>

	 <?php echo $form->errorSummary($model); ?>
	 
    <?php echo $form->textFieldRow($model, 'name'); ?>
	<?php echo $form->textFieldRow($model, 'email'); ?>
	<?php echo $form->textFieldRow($model, 'subject'); ?>
    <?php echo $form->textAreaRow($model, 'body', array('class'=>'span3', 'rows'=>5)); ?>
 
	 <div class="well">
		 <?php if(CCaptcha::checkRequirements()): ?>
			 <?php echo $form->labelEx($model,'verifyCode'); ?>
			 <div>
			 <?php $this->widget('CCaptcha'); ?>
			 <?php echo $form->textField($model,'verifyCode'); ?>
			 </div>
			 <div class="hint">Please enter the letters as they are shown in the image above.
			 <br/>Letters are not case-sensitive.</div>
			 <?php echo $form->error($model,'verifyCode'); ?>
		 <?php endif; ?>
	</div>

</fieldset>
 
<div class="form-actions">
    <?php echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> Submit', array('class'=>'btn btn-primary', 'type'=>'submit')); ?>
    <?php echo CHtml::htmlButton('<i class="icon-ban-circle"></i> Reset', array('class'=>'btn', 'type'=>'reset')); ?>
</div>
 
<?php $this->endWidget(); ?>
</div>

<?php endif; ?>
