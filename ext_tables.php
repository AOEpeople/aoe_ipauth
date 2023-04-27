<?php
defined('TYPO3') or die();

// IP
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_aoeipauth_domain_model_ip',
    'EXT:aoe_ipauth/Resources/Private/Language/locallang_csh_tx_aoeipauth_domain_model_ip.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'fe_users',
    'EXT:aoe_ipauth/Resources/Private/Language/locallang_csh_fe_users.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'fe_groups',
    'EXT:aoe_ipauth/Resources/Private/Language/locallang_csh_fe_groups.xlf'
);

// registering reports
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['aoe_ipauth'] = array(
    'AOE\\AoeIpauth\\Report\\IpGroupAuthenticationStatus',
    'AOE\\AoeIpauth\\Report\\IpUserAuthenticationStatus',
);
