<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Do not show the IP records in the listing
$allowedTablesTS = "
	mod.web_list.deniedNewTables := addToList(tx_aoeipauth_domain_model_ip)
	mod.web_list.hideTables := addToList(tx_aoeipauth_domain_model_ip)
";

t3lib_extMgm::addPageTSConfig($allowedTablesTS);
?>