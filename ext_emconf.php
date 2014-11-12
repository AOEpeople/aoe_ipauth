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
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.1dev',
	'constraints' => array(
		'depends' => array(
			'extbase' => '1.4',
			'fluid' => '1.4',
			'typo3' => '6.2',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
