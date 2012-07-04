<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>

<?php echo "<?php \$form=\$this->beginWidget('ext.bootstrap.widgets.BootActiveForm', array(
	'id'=>'".$this->class2id($this->modelClass)."-form',
	'enableAjaxValidation'=>false,
)); ?>\n"; ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo "<?php echo \$form->errorSummary(\$model); ?>\n"; ?>

<?php
foreach($this->tableSchema->columns as $column)
{
	
	//echo CVarDumper::dumpAsString($column);
	
	if($column->autoIncrement)
		continue;
	else if($column->isPrimaryKey)
		continue;
	else if(in_array($column->name, array('timestamp', 'entrydate'))) 
		continue;
?>
	<?php echo "<?php echo ".$this->generateActiveField($this->modelClass,$column)."; ?>\n"; ?>

<?php
}
?>
	
	<div class="actions">
		<?php echo "<?php echo CHtml::submitButton(\$model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn primary')); ?>\n"; ?>
	</div>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>