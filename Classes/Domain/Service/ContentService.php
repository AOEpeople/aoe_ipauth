<?php
namespace AOE\AoeIpauth\Domain\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use AOE\AoeIpauth\Utility\EnableFieldsUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ContentService
 *
 * @package AOE\AoeIpauth\Domain\Service
 */
class ContentService implements \TYPO3\CMS\Core\SingletonInterface {

	const CONTENT_TABLE = 'tt_content';
	const PAGES_TABLE = 'pages';
	const PAGES_OVERLAY_TABLE = 'pages_language_overlay';

	/**
	 * Returns true if the page has content elements
	 * that depend on logged in users/user groups
	 *
	 * @param int $uid
	 * @param int $languageUid
	 * @return bool
	 */
	public function isPageUserCustomized($uid, $languageUid) {
		$rows = $this->findUserCustomizedContentByPageId($uid, $languageUid);
		$isCustomizedByContent = (0 < count($rows));
		$isCustomizedByPage = $this->isPageBareUserCustomized($uid, $languageUid);
		return ($isCustomizedByContent || $isCustomizedByPage);
	}

	/**
	 * Checks if a page itself is user customized
	 * No matter what the languageUid is, we only need to check the original page
	 *
	 * @param int $uid
	 * @param int $languageUid
	 * @return bool
	 */
	public function isPageBareUserCustomized($uid, $languageUid) {
		$enableFields = EnableFieldsUtility::enableFields(self::CONTENT_TABLE);
		$pages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid,pid',
			self::PAGES_TABLE,
			'fe_group != 0 AND uid = ' . intval($uid) . ' ' . $enableFields
		);
		$isPageCustomized = (count($pages) > 0);
		return $isPageCustomized;
	}

	/**
	 * Returns content elements that depend on logged in users/user groups
	 * TODO: this will not consider:
	 * a) content that is displayed here but is only referenced, and
	 * b) content that is on the page but not used
	 * (not actually linked to the page)
	 *
	 * @param int $uid fe_groups uid
	 * @param int $languageUid
	 * @return array
	 */
	public function findUserCustomizedContentByPageId($uid, $languageUid) {
		$enableFields = EnableFieldsUtility::enableFields(self::CONTENT_TABLE);
		$ttContent = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid,pid',
			self::CONTENT_TABLE,
			'fe_group > 0 AND sys_language_uid = ' . intval($languageUid) . ' AND pid = ' . intval($uid) . ' ' . $enableFields);
		return $ttContent;
	}
}