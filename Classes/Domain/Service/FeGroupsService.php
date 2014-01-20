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
class Tx_AoeIpauth_Domain_Service_FeGroupsService implements t3lib_Singleton {

	/**
	 * @var t3lib_pageSelect
	 */
	protected $pageSelect = NULL;

	/**
	 * @var Tx_AoeIpauth_Domain_Service_IpService
	 */
	protected $ipService = NULL;

	/**
	 * Returns all fe_groups with ip authentication enabled
	 *
	 * @return array
	 */
	public function findAllGroupsWithIpAuthentication() {
		$enableFields = $this->getPageSelect()->enableFields('fe_groups');
		$groups = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,pid,hidden,deleted,title', 'fe_groups', 'tx_aoeipauth_ip > 0' . $enableFields);
		if (empty($groups)) {
			return array();
		}

		// Enrich with IPs
		$finalGroups = array();

		foreach ($groups as $group) {
			$uid = $group['uid'];
			$matchedIps = $this->getIpService()->findIpsByFeGroupId($uid);
			// Skip groups that do not find a corresponding ip
			if (empty($matchedIps)) {
				continue;
			}
			// Inject the matched ips to the group
			$group['tx_aoeipauth_ip'] = $matchedIps;
			$finalGroups[] = $group;
		}

		return $finalGroups;
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

	/**
	 * @return Tx_AoeIpauth_Domain_Service_IpService
	 */
	protected function getIpService() {
		if (NULL === $this->ipService) {
			$this->ipService = t3lib_div::makeInstance('Tx_AoeIpauth_Domain_Service_IpService');
		}
		return $this->ipService;
	}

}
?>