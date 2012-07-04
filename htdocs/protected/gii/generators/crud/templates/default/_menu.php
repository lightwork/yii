<?php echo "<?php\n"; ?>

$menu = array();
	
if(Yii::app()->user->isAdmin) {
	$menu[] = array('label'=>'List <?php echo $this->modelClass; ?>','url'=>array('index'), 'icon'=>'icon-list');
	$menu[] = array('label'=>'Create <?php echo $this->modelClass; ?>','url'=>array('create'), 'icon'=>'icon-pencil');
	$menu[] = array('label'=>'Manage <?php echo $this->modelClass; ?>','url'=>array('admin'), 'icon'=>'icon-th-list');
}

if(isset($model) && isset($model->id)) {
	$menu[] = array('label'=>'Update <?php echo $this->modelClass; ?>','url'=>array('update','id'=>$model->id), 'icon'=>'icon-edit');
	$menu[] = array('label'=>'Delete <?php echo $this->modelClass; ?>','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?'), 'icon'=>'icon-remove');
}

$this->menu = $menu;

<?php echo "
?>\n"; ?>