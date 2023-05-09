<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 AOE GmbH <dev@aoe.com>
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

defined('TYPO3') or die();

$GLOBALS['TCA']['tx_aoeipauth_domain_model_ip'] = array(
    'ctrl' => array(
        'title'    => 'LLL:EXT:aoe_ipauth/Resources/Private/Language/locallang_db.xlf:tx_aoeipauth_domain_model_ip',
        'label' => 'description',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'versioning' => false,
        'versioningWS' => false,
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        \TYPO3\CMS\Core\Utility\PathUtility::stripPathSitePrefix(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('aoe_ipauth')) . 'Resources/Public/Icons/tx_aoeipauth_domain_model_ip.png'
    ),
    'interface' => array(
    ),
    'types' => array(
        '1' => array('showitem' => 'hidden,--palette--;;1,ip,description'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:aoe_ipauth/Resources/Private/Language/locallang_db.xlf:tx_aoeipauth_domain_model_ip.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'starttime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ),
        'endtime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ),
        'ip' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:aoe_ipauth/Resources/Private/Language/locallang_db.xlf:tx_aoeipauth_domain_model_ip.ip',
            'config' => array(
                'type' => 'input',
                'size' => 60,
                'eval' => 'trim,required'
            ),
        ),
        'description' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:aoe_ipauth/Resources/Private/Language/locallang_db.xlf:tx_aoeipauth_domain_model_ip.description',
            'config' => array(
                'type' => 'input',
                'size' => 120,
                'eval' => 'required'
            ),
        ),
        'fe_user' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'fe_group' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'range_type' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
    ),
);
