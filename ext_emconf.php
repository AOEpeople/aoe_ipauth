<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'IP Authentication',
	'description' => 'Authenticates users based on IP address settings',
	'category' => 'services',
	'author' => 'DEV',
	'author_email' => 'dev@aoe.com',
	'author_company' => 'AOE GmbH',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.5.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.1-7.99.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
