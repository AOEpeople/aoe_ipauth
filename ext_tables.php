<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// IP
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'tx_aoeipauth_domain_model_ip',
	'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_tx_aoeipauth_domain_model_ip.xml'
);

$TCA['tx_aoeipauth_domain_model_ip'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:tx_aoeipauth_domain_model_ip',
		'label' => 'ip',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'delete' => 'deleted',
		'versioning' => FALSE,
		'versioningWS' => FALSE,
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Ip.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_aoeipauth_domain_model_ip.png'
	),
);

// Add IP Options to fe_users table
$tempColumns = array(
	'tx_aoeipauth_ip' => array(
		'label' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:fe_users.tx_aoeipauth_ip',
		'exclude' => 1,
		'config' => Array (
			'type' => 'inline',
			'foreign_table' => 'tx_aoeipauth_domain_model_ip',
			'foreign_field' => 'fe_user',
			'maxitems' => 9999,
			'appearance' => array(
				'collapse' => 1,
				'levelLinksPosition' => 'top',
				'showSynchronizationLink' => 0,
				'showPossibleLocalizationRecords' => 0,
				'showAllLocalizationLink' => 0
			),
		),
	),
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
	'fe_users',
	$tempColumns,
	1
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'fe_users',
	'tx_aoeipauth_ip;;;;1-1-1',
	'',
	'after:lockToDomain'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'fe_users',
	'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_fe_users.xml'
);
unset($tempColumns);

// Add IP Options to fe_groups table
$tempColumns = array(
	'tx_aoeipauth_ip' => array(
		'label' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:fe_groups.tx_aoeipauth_ip',
		'exclude' => 1,
		'config' => Array (
			'type' => 'inline',
			'foreign_table' => 'tx_aoeipauth_domain_model_ip',
			'foreign_field' => 'fe_group',
			'maxitems' => 9999,
			'appearance' => array(
				'collapse' => 1,
				'levelLinksPosition' => 'top',
				'showSynchronizationLink' => 0,
				'showPossibleLocalizationRecords' => 0,
				'showAllLocalizationLink' => 0
			),
		),
	),
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
	'fe_groups',
	$tempColumns,
	1
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'fe_groups',
	'tx_aoeipauth_ip;;;;1-1-1',
	'',
	'after:lockToDomain'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'fe_groups',
	'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_fe_groups.xml'
);
unset($tempColumns);

if ('BE' === TYPO3_MODE) {
	// registering reports
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers'][$_EXTKEY] = array(
		'AOE\\AoeIpauth\\Report\\IpGroupAuthenticationStatus',
		'AOE\\AoeIpauth\\Report\\IpUserAuthenticationStatus',
	);
}
