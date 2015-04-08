<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// IP
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'tx_aoeipauth_domain_model_ip',
	'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_tx_aoeipauth_domain_model_ip.xml'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'fe_users',
	'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_fe_users.xml'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'fe_groups',
	'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_fe_groups.xml'
);

if ('BE' === TYPO3_MODE) {
	// registering reports
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers'][$_EXTKEY] = array(
		'AOE\\AoeIpauth\\Report\\IpGroupAuthenticationStatus',
		'AOE\\AoeIpauth\\Report\\IpUserAuthenticationStatus',
	);
}
