<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php
echo "<?php\n";
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	\$model->{$nameColumn},
);\n";
?>

$this->renderPartial('_menu', array('model'=>$model));

?>

<h1>View <?php echo $this->modelClass." #<?php echo \$model->{$this->tableSchema->primaryKey}; ?>"; ?></h1>

<?php echo "<?php"; ?> $this->widget('ext.bootstrap.widgets.BootDetailView', array(
	'data'=>$model,
	'type'=>'striped bordered condensed',
	'attributes'=>array(
<?php
foreach($this->tableSchema->columns as $column)
	if($column->name == $this->tableSchema->primaryKey) : ?>
		array(
            'label'=>'id',
            'type'=>'raw',
            'value'=>$model-><?php echo $this->tableSchema->primaryKey; ?> . ' ' . CHtml::link(
            	CHtml::encode('(edit)'),
            	array('update','id'=>$model->id)
            ),
        ),
    <?php else :
	echo "\t\t'".$column->name."',\n";
    endif;
?>
	),
)); ?>
