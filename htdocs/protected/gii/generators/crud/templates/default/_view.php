<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<div class="view">

<?php
echo "\t<b><?php echo CHtml::encode(\$data->getAttributeLabel('{$this->tableSchema->primaryKey}')); ?>:</b>\n";
echo "\t<?php echo CHtml::link(CHtml::encode(\$data->{$this->tableSchema->primaryKey}), array('view', 'id'=>\$data->{$this->tableSchema->primaryKey})); ?>\n\t<br />\n\n";


echo "
	<?php \$this->widget('ext.bootstrap.widgets.BootDetailView',array(
		'data'=>\$data,
		'type'=>'striped bordered condensed',
		'attributes'=>array("; 

$count=0;
foreach($this->tableSchema->columns as $column)
{
	if($column->isPrimaryKey) {
		echo "
			array( 
	            'label'=>'id',
	            'type'=>'raw',
	            'value'=>CHtml::link(
	            	CHtml::encode(\$data->{$this->tableSchema->primaryKey}),
	            	array('view','{$this->tableSchema->primaryKey}'=>\$data->{$this->tableSchema->primaryKey})
	            ),
	        ),";
	}
	else {
		echo "
			'{$column->name}',";
	}
}
echo "
		),
	)); ?>

";


?>

</div>