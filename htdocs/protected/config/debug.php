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

			/*
			'db'=>array(
				'connectionString' => 'mysql:host=localhost;dbname=velaapp',
				'emulatePrepare' => true,
				'username' => 'velaapp',
				'password' => 've1asql',
				'charset' => 'utf8',
			),
			*/

			'log'=>array(
				'class'=>'CLogRouter',
				'routes'=>array(
					// General log
					array(
						'class'=>'CFileLogRoute',
						'levels'=>'trace,info,warning,error',
						'logFile'=>'app.log',
						'categories'=>'app',
					),
					// uncomment the following to show log messages on web pages

					/*
					 array(
					 	'class'=>'CWebLogRoute',
					 	'levels'=>'trace',
					 )
*/
				),
			),

		), // end components

	)
);

?>