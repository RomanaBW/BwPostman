<?php
// This is global bootstrap for autoloading

require __DIR__ . '/_support/BwRunFailed.php';

/*
\Codeception\Configuration::$defaultSuiteSettings['paths'] = [
	'log' => getenv('BW_TEST_LOG_PATH'),
];

\Codeception\Configuration::$defaultSuiteSettings['modules']['config'] = [
	'Webdriver' => [
		'url' => getenv('BW_TEST_URL'),
		'window_size' => '1440x900',
        'browser' => 'chrome',
        'port' => '4445',
        'connection_timeout' => '60',
        'restart' => false
		],
	'Db' => [
		'dsn' => 'mysql:host=' . getenv('BW_TEST_DB_HOST') . ';dbname=' . getenv('BW_TEST_DB_NAME'),
		'user' => getenv('BW_TEST_DB_USER'),
		'password' => getenv('BW_TEST_DB_PW'),
		'populate' => false,
		'cleanup' => false
	]
];
*/
