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
class Tx_AoeIpauth_Typo3_Service_Authentication extends tx_sv_authbase {

	// TODO: do we really need to getUser?

	/**
	 * @var Tx_AoeIpauth_Service_IpMatchingService
	 */
	protected $ipMatchingService = NULL;

	/**
	 * @var Tx_AoeIpauth_Domain_Service_FeGroupsService
	 */
	protected $feGroupsService = NULL;

	/**
	 * Get the group list
	 *
	 * @param string $user
	 * @param array $knownGroups
	 * @return array
	 */
	public function getGroups($user, $knownGroups) {

		// Do not respond to non-FE group calls
		if('getGroupsFE' != $this->mode) {
			return $knownGroups;
		}

		$clientIp = $this->authInfo['REMOTE_ADDR'];
		$groups = $this->findAllGroupsWithIpAuthentication();

		if (empty($groups)) {
			return $knownGroups;
		}

		// Walk each group and check if it matches
		foreach ($groups as $group) {
			$groupUid = $group['uid'];
			$groupIps = $group['tx_aoeipauth_ip'];
			unset($group['tx_aoeipauth_ip']);

			$isWhitelisted = FALSE;
			while (!$isWhitelisted && !empty($groupIps)) {
				$ipWhitelist = array_pop($groupIps);
				$isWhitelisted = $this->getIpMatchingService()->isIpAllowed($clientIp, $ipWhitelist);
			}

			if ($isWhitelisted) {
				$knownGroups[$groupUid] = $group;
			}
		}

		return $knownGroups;
	}

	/**
	 * Finds all groups with IP authentication enabled
	 *
	 * @return array
	 */
	protected function findAllGroupsWithIpAuthentication() {
		$groups = $this->getFeGroupsService()->findAllGroupsWithIpAuthentication();
		return $groups;
	}

	/**
	 * @return Tx_AoeIpauth_Service_IpMatchingService
	 */
	protected function getIpMatchingService() {
		if (NULL === $this->ipMatchingService) {
			$this->ipMatchingService = t3lib_div::makeInstance('Tx_AoeIpauth_Service_IpMatchingService');
		}
		return $this->ipMatchingService;
	}

	/**
	 * @return Tx_AoeIpauth_Domain_Service_FeGroupsService
	 */
	protected function getFeGroupsService() {
		if (NULL === $this->feGroupsService) {
			$this->feGroupsService = t3lib_div::makeInstance('Tx_AoeIpauth_Domain_Service_FeGroupsService');
		}
		return $this->feGroupsService;
	}
}
?>