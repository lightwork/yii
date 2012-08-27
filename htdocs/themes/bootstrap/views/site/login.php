<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<h1>Login</h1>

<p>Please fill out the following form with your login credentials:</p>

<div class="span6">
<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'contact-form',
	'type'=>'horizontal',
)); ?>


<fieldset>

	 <p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model, 'username'); ?>
	<?php echo $form->passwordFieldRow($model, 'password'); ?>
	<?php echo $form->checkBoxRow($model, 'rememberMe'); ?>

</fieldset>

<div class="form-actions">
	<?php echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> Submit', array('class'=>'btn btn-primary', 'type'=>'submit')); ?>
	<?php echo CHtml::htmlButton('<i class="icon-ban-circle"></i> Reset', array('class'=>'btn', 'type'=>'reset')); ?>
</div>

<?php $this->endWidget(); ?>
</div>

