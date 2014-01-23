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

	/**
	 * @var Tx_AoeIpauth_Service_IpMatchingService
	 */
	protected $ipMatchingService = NULL;

	/**
	 * @var Tx_AoeIpauth_Domain_Service_FeEntityService
	 */
	protected $feEntityService = NULL;

	/**
	 * @var Tx_AoeIpauth_Domain_Service_IpService
	 */
	protected $ipService = NULL;

	/**
	 * Makes sure the TCA is readable, necessary for enableFields to work
	 * Is de-facto called when using the Preview BE Module
	 *
	 * @return void
	 */
	protected function safeguardContext() {
		if (!isset($GLOBALS['TSFE'])) {
			return;
		}
		if (!isset($GLOBALS['TCA'][Tx_AoeIpauth_Domain_Service_FeEntityService::TABLE_USER])) {
			$GLOBALS['TSFE']->getCompressedTCarray();
		}
	}

	/**
	 * Gets the user automatically
	 *
	 * @return bool
	 */
	public function getUser() {
		// Do not respond to non-fe users and login attempts
		if('getUserFE' != $this->mode || 'login' == $this->login['status']) {
			return FALSE;
		}

		$this->safeguardContext();

		$clientIp = $this->authInfo['REMOTE_ADDR'];
		$ipAuthenticatedUsers = $this->findAllUsersByIpAuthentication($clientIp);

		if (empty($ipAuthenticatedUsers)) {
			return FALSE;
		}

		$user = array_pop($ipAuthenticatedUsers);
		return $user;
	}

	/**
	 * Authenticate a user
	 * Return 200 if the IP is right. This means that no more checks are needed. Otherwise authentication may fail because we may don't have a password.
	 *
	 * @param array Data of user.
	 * @return bool
	 */
	public function authUser($user) {

		$this->safeguardContext();

		$authCode = 100;

		// Do not respond to non-fe users and login attempts
		if('FE' != $this->authInfo['loginType'] || 'login' == $this->login['status']) {
			return $authCode;
		}
		if (!isset($user['uid'])) {
			return $authCode;
		}

		$clientIp = $this->authInfo['REMOTE_ADDR'];
		$userId = $user['uid'];

		$ipMatches = $this->doesCurrentUsersIpMatch($userId, $clientIp);

		if ($ipMatches) {
			$authCode = 200;
		}

		return $authCode;
	}

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

		$this->safeguardContext();

		$clientIp = $this->authInfo['REMOTE_ADDR'];
		$ipAuthenticatedGroups = $this->findAllGroupsByIpAuthentication($clientIp);

		if (!empty($ipAuthenticatedGroups)) {
			$knownGroups = array_merge($ipAuthenticatedGroups, $knownGroups);
		}

		return $knownGroups;
	}

	/**
	 * Returns TRUE if the userId's associated IPs match the client IP
	 *
	 * @param int $userId
	 * @param string $clientIp
	 * @return bool
	 */
	protected function doesCurrentUsersIpMatch($userId, $clientIp) {
		$isMatch = FALSE;
		$ips = $this->getIpService()->findIpsByFeUserId($userId);

		foreach ($ips as $ipWhitelist) {
			if ($this->getIpMatchingService()->isIpAllowed($clientIp, $ipWhitelist)) {
				$isMatch = TRUE;
				break;
			}
		}
		return $isMatch;
	}

	/**
	 * Finds all users with IP authentication enabled
	 *
	 * @param string $ip
	 * @return array
	 */
	protected function findAllUsersByIpAuthentication($ip) {
		$users = $this->getFeEntityService()->findAllUsersAuthenticatedByIp($ip);
		return $users;
	}

	/**
	 * Finds all groups with IP authentication enabled
	 *
	 * @param string $ip
	 * @return array
	 */
	protected function findAllGroupsByIpAuthentication($ip) {
		$groups = $this->getFeEntityService()->findAllGroupsAuthenticatedByIp($ip);
		return $groups;
	}

	/**
	 * @return Tx_AoeIpauth_Domain_Service_FeEntityService
	 */
	protected function getFeEntityService() {
		if (NULL === $this->feEntityService) {
			$this->feEntityService = t3lib_div::makeInstance('Tx_AoeIpauth_Domain_Service_FeEntityService');
		}
		return $this->feEntityService;
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

	/**
	 * @return Tx_AoeIpauth_Service_IpMatchingService
	 */
	protected function getIpMatchingService() {
		if (NULL === $this->ipMatchingService) {
			$this->ipMatchingService = t3lib_div::makeInstance('Tx_AoeIpauth_Service_IpMatchingService');
		}
		return $this->ipMatchingService;
	}
}
?>