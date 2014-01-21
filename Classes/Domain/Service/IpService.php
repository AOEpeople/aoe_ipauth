<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 DEV <dev@aoemedia.de>, aoemedia GmbH
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

/**
 *
 *
 * @package aoe_ipauth
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_AoeIpauth_Domain_Service_IpService implements t3lib_Singleton {

	const TABLE = 'tx_aoeipauth_domain_model_ip';

	/**
	 * @var t3lib_pageSelect
	 */
	protected $pageSelect = NULL;

	/**
	 * Returns all ip domain model records that belong to a given fe_user uid
	 *
	 * @param int $uid fe_users uid
	 * @return array
	 */
	public function findIpsByFeUserId($uid) {
		return $this->findIpsByField('fe_user', $uid);
	}

	/**
	 * Returns all ip domain model records that belong to a given fe_groups uid
	 *
	 * @param int $uid fe_groups uid
	 * @return array
	 */
	public function findIpsByFeGroupId($uid) {
		return $this->findIpsByField('fe_group', $uid);
	}

	/**
	 * Finds IPs from the table by a given field and field value
	 *
	 * @param string $field
	 * @param int $value
	 * @return array
	 */
	protected function findIpsByField($field, $value) {
		$enableFields = $this->getPageSelect()->enableFields(self::TABLE);
		$ips = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('ip', self::TABLE, $field . ' = ' . intval($value) . ' ' . $enableFields);
		if (empty($ips)) {
			return array();
		}
		$finalIps = array();
		foreach ($ips as $record) {
			$finalIps[] = $record['ip'];
		}

		return $finalIps;
	}

	/**
	 * @return t3lib_pageSelect
	 */
	protected function getPageSelect() {
		if (NULL === $this->pageSelect) {
			$this->pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
		}
		return $this->pageSelect;
	}

}
?>