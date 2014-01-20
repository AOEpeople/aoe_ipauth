<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// IP
t3lib_extMgm::addLLrefForTCAdescr('tx_aoeipauth_domain_model_ip', 'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_tx_aoeipauth_domain_model_ip.xml');
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Ip.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_aoeipauth_domain_model_ip.png'
	),
);

// Add IP Options to fe_users table
t3lib_div::loadTCA('fe_users');
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
t3lib_extMgm::addTCAcolumns('fe_users', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_aoeipauth_ip;;;;1-1-1', '', 'after:lockToDomain');
t3lib_extMgm::addLLrefForTCAdescr('fe_users', 'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_fe_users.xml');
unset($tempColumns);

// Add IP Options to fe_groups table
t3lib_div::loadTCA('fe_groups');
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
t3lib_extMgm::addTCAcolumns('fe_groups', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('fe_groups','tx_aoeipauth_ip;;;;1-1-1', '', 'after:lockToDomain');
t3lib_extMgm::addLLrefForTCAdescr('fe_groups', 'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_fe_groups.xml');
unset($tempColumns);

?>