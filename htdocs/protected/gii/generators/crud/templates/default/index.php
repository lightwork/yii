<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php
echo "<?php\n";
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label',
);\n";
?>

$this->renderPartial('_menu');

?>

<h1><?php echo $label; ?></h1>

<?php echo "<?php"; ?> $this->widget('ext.bootstrap.widgets.BootListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
