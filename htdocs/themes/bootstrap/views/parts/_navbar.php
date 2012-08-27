<?php

if(Yii::app()->user->isGuest) {
	$navitems = array(
		array(
			'class'=>'bootstrap.widgets.TbMenu',
			'items'=>array(
				array('label'=>'Login', 'url'=>array('/site/login')),
			)
		)
	);
}
else {
	$navitems = array(
		array(
			'class'=>'bootstrap.widgets.TbMenu',
			'items'=>array(
				array('label'=>'Home', 'url'=>'/'),
				array('label'=>'Links', 'url'=>'#', 'items'=>array(
					array('label'=>'About', 'url'=>$this->createUrl('site/page', array('view'=>'about')), 'icon'=>'icon-gift'),
					array('label'=>'Contact', 'url'=>array('site/contact'), 'icon'=>'icon-th-list'),
				)),
			),
		),
		//'<form class="navbar-search pull-left" action=""><input type="text" class="search-query span2" placeholder="Search"></form>',
		array(
			'class'=>'bootstrap.widgets.TbMenu',
			'htmlOptions'=>array('class'=>'pull-right'),
			'items'=>array(
				//array('label'=>'Profile', 'url'=>array('/vendor/view', 'id'=>Yii::app()->user->id)),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
				'---',
			),
		),
	);
}

$this->widget('bootstrap.widgets.TbNavbar', array(
	'type'=>'inverse',
	'fixed'=>false,
	'brand'=>'Yii Dev App',
	'brandUrl'=>Yii::app()->createUrl('/site/index'),
	'collapse'=>true, // requires bootstrap-responsive.css
	'items'=>$navitems
));

?>
