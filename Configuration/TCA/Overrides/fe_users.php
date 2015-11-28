<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

defined('TYPO3_MODE') or die();

// Add IP Options to fe_users table
$tempColumns = array(
	'tx_aoeipauth_ip' => array(
		'label' => 'LLL:EXT:aoe_ipauth/Resources/Private/Language/locallang_db.xlf:fe_users.tx_aoeipauth_ip',
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
	TRUE
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'fe_users',
	'tx_aoeipauth_ip;;;;1-1-1',
	'',
	'after:lockToDomain'
);

unset($tempColumns);