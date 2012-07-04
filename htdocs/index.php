<?php

date_default_timezone_set('UTC');

// php.ini sets this to no-cache by default, which prevents any dynamic images
// from being cached by the browser. Notes in the php docs show the following
// setting to be the best. Time will tell.
//
// This value must be set before a session is started.
//
// http://www.php.net/manual/en/function.session-cache-limiter.php#82048

session_cache_limiter('private_no_expire, must-revalidate');

if (file_exists(__DIR__.'/index-test.php')) {
	require_once __DIR__.'/index-test.php';
}

$yii=dirname(__FILE__).'/../yii-1.1.10.r3566/framework/yii.php';
require_once($yii);

$config = dirname(__FILE__).'/protected/config/main.php';

// include_once($config);
// echo '<pre>';
// echo var_export($config, true);
// echo '</pre>';
// exit;

require_once(dirname(__FILE__).'/protected/components/LWWebApplication.php');
Yii::createApplication('LWWebApplication', $config)->run();
