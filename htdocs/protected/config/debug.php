<?php

$config = CMap::mergeArray(
	$config,
	array(

		'modules' => array(
			'gii'=>array(
				'class'=>'system.gii.GiiModule',
				'password'=>'gii',
				// If removed, Gii defaults to localhost only. Edit carefully to taste.
				'ipFilters'=>array('127.0.0.1','::1', '10.0.1.201', '*'),
				'generatorPaths'=>array(
					'application.gii.generators',
				)
			),
		), // end modules

		'components'=>array(

			'cache' => array('class' => 'CDummyCache'),

			'assetManager'=>array(
				'linkAssets'=>true,
			),

		), // end components

	)
);

?>