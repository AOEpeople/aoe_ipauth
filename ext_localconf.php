<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
$_EXTKEY = 'aoe_ipauth';
$backendConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('backend');

if ('BE' === TYPO3_MODE) {
    // Do not show the IP records in the listing
    $allowedTablesTs = '
		mod.web_list.deniedNewTables := addToList(tx_aoeipauth_domain_model_ip)
		mod.web_list.hideTables := addToList(tx_aoeipauth_domain_model_ip)
	';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig($allowedTablesTs);

    // Hooks
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$_EXTKEY] = \AOE\AoeIpauth\Hooks\Tcemain::class;
} elseif ('FE' === TYPO3_MODE) {
    $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get($_EXTKEY);
    $GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'] =
        isset($extensionConfiguration['fetchFeUserIfNoSession']) ? boolval($extensionConfiguration['fetchFeUserIfNoSession']) : 1;
    unset($extensionConfiguration);
}

// IP Authentication Service
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService($_EXTKEY, 'auth', 'tx_aoeipauth_typo3_service_authentication',
    array(
        'title' => 'IP Authentication',
        'description' => 'Authenticates against IP addresses and ranges.',
        'subtype' => 'authUserFE,getUserFE,getGroupsFE',
        'available' => true,
        // Must be higher than for tx_sv_auth (50) or tx_sv_auth will deny request unconditionally
        'priority' => 80,
        'quality' => 50,
        'os' => '',
        'exec' => '',
        'classFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Typo3/Service/Authentication.php',
        'className' => 'AOE\AoeIpauth\Typo3\Service\Authentication',
    )
);
