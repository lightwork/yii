<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

setlocale(LC_MONETARY, 'en_US');
date_default_timezone_set('UTC');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$config = array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Yii Development Base',
	'theme'=>'bootstrap',

	// preloading 'log' component
	'preload'=>array(
		'log',
		'bootstrap',
		'less',
	),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.models.base.*',
		'application.models.reporting.*',
		'application.models.forms.*',
		'application.models.forms.schedule.*',
		'application.components.*',
		'application.collections.*',
		'application.helpers.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
	),

	// application components
	'components'=>array(

		'cache' => array('class' => 'system.caching.CFileCache'),

		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'class' => 'WebUser',
			//'stateKeyPrefix'=>'customKeyPrefix',
		),

		'authManager'=>array(
			'class'=>'CDbAuthManager',
			'connectionID'=>'db',
		),

		'request'=>array(
			'class'=>'application.components.LWHttpRequest'
		),

		'clientScript'=>array(
			'packages'=>array(
				'mootools' => array(
					'baseUrl' => '/js/libs/mootools',
					'js' => array('core-1.4.5.min.js', 'more-1.4.0.1.min.js', 'utils.js', 'extras/Array.extras.js', '../jquery/noconflict.js')
				),
				'framework' => array(
					'baseUrl' => '/js/libs',
					'js' => array('framework.js'),
					'depends' => array('mootools')
				),
			)
		),

		'bootstrap'=>array(
			'class'=>'ext.bootstrap.components.Bootstrap',
			'responsiveCss'=>false,
		),

	    'less'=>array(
	        'class'=>'ext.less.components.LessCompiler',
	        'forceCompile'=>false, // indicates whether to force compiling
	        'paths'=>array(
	        	// These files are located relative to the webroot
	            'less/test.less'=>'css/test.css',
	        ),
	    ),

		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName' => false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),

		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),

		'session' => array (
			// Cannot use dots in the session id :(
			//'sessionName' => 'custom_session_id',
			//'class' => 'system.web.CDbHttpSession',
			//'connectionID' => 'db',
		),

		'globaljs'=>array(
			'class'=>'application.extensions.globaljs.LWGlobalJs'
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);

$debugConfig = __DIR__ . '/debug.php';
if (file_exists($debugConfig))
{
	require_once($debugConfig);
}

return $config;