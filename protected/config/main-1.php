<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
	Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap-theme');


// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Monitoring Kegiatan',
	
	// bootstrap
	'theme'=>'bootstrap',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',

	// bootstrap
		'ext.bootstrap-theme.widgets.*',
		'ext.bootstrap-theme.helpers.*',
		'ext.bootstrap-theme.behaviors.*',

		'application.modules.spj.models.*',
	),

	'modules'=>array(
/*		'spj',
		'skp',
		'bbm',
*/		// uncomment the following to enable the Gii tool
		
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'gii',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
			'generatorPaths'=>array(
			    'ext.bootstrap-theme.gii',
			),
		),
		*/
		
	),

	// application components
	'components'=>array(

		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'class'=>'application.components.EWebUser',
		),

		// uncomment the following to enable URLs in path-format
/*		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>true,
			'rules'=>array(
				''=>'site/index',
				array('class'=>'application.components.CustomUrl')
			),
		),
		
*/	


	/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/

		// database settings are configured in database.php
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=monika_db',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),

		'sqlite'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/sqlite.db',
		),

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

		'contentCompactor'=>array(
			'class'=>'application.extensions.contentCompactor.ContentCompactor',
		),

		'tad' => array(
		    'class' => 'application.extensions.tad.tadabsen'
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// apakah akan menggunakan fitur autocomplete untuk proses login??? true=ya, false=tidak
		'autocomplete'=>false,
		// apakah akan menggunakan fitur mingguan untuk pegawai??? true=ya, false=tidak
		'mingguan'=>true,

		'versi'=>require(dirname(__FILE__).'/versi.php'),
		'adminEmail'=>'ipds3303@bps.go.id',

		// alamat IP mesin presensi
		'ip_presensi'=>'10.133.2.121',
		'satuankerja'=>'BPS Kabupaten Banyumas',
		'namaibukota'=>'Banyumas',
	),
);
