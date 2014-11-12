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
 * Class FeEntityService
 *
 * @package AOE\AoeIpauth\Domain\Service
 */
class FeEntityService implements \TYPO3\CMS\Core\SingletonInterface {

	const TABLE_GROUP = 'fe_groups';
	const TABLE_USER = 'fe_users';

	/**
	 * @var \AOE\AoeIpauth\Domain\Service\IpService
	 */
	protected $ipService = NULL;

	/**
	 * @var \AOE\AoeIpauth\Service\IpMatchingService
	 */
	protected $ipMatchingService = NULL;

	/**
	 * Finds all groups that would be authenticated against a certain IP
	 *
	 * @param string $ip
	 * @return array
	 */
	public function findAllGroupsAuthenticatedByIp($ip) {
		$groups = $this->findEntitiesAuthenticatedByIp($ip, self::TABLE_GROUP);
		return $groups;
	}

	/**
	 * Finds all groups that would be authenticated against a certain IP
	 *
	 * @param string $ip
	 * @return array
	 */
	public function findAllUsersAuthenticatedByIp($ip) {
		$groups = $this->findEntitiesAuthenticatedByIp($ip, self::TABLE_USER);
		return $groups;
	}

	/**
	 * Returns all fe_groups with ip authentication enabled
	 * Convenience method for "findEntitiesWithIpAuthentication"
	 *
	 * @return array
	 */
	public function findAllGroupsWithIpAuthentication() {
		$groups = $this->findEntitiesWithIpAuthentication(self::TABLE_GROUP);
		return $groups;
	}

	/**
	 * Returns all fe_users with ip authentication enabled
	 * Convenience method for "findEntitiesWithIpAuthentication"
	 *
	 * @return array
	 */
	public function findAllUsersWithIpAuthentication() {
		$users = $this->findEntitiesWithIpAuthentication(self::TABLE_USER);
		return $users;
	}

	/**
	 * Finds all entities that would be authenticated against a certain IP
	 *
	 * @param string $ip
	 * @param string $table
	 * @return array
	 */
	protected function findEntitiesAuthenticatedByIp($ip, $table) {
		$authenticatedEntities = array();
		$entities = $this->findEntitiesWithIpAuthentication($table);

		if (empty($entities)) {
			return $authenticatedEntities;
		}

		// Walk each group and check if it matches
		foreach ($entities as $entity) {
			$uid = $entity['uid'];
			$ips = $entity['tx_aoeipauth_ip'];
			unset($entity['tx_aoeipauth_ip']);

			$isWhitelisted = FALSE;
			while (!$isWhitelisted && !empty($ips)) {
				$ipWhitelist = array_pop($ips);
				$isWhitelisted = $this->getIpMatchingService()->isIpAllowed($ip, $ipWhitelist);
			}

			if ($isWhitelisted) {
				$authenticatedEntities[$uid] = $entity;
			}
		}
		return $authenticatedEntities;
	}

	/**
	 * Finds entities with IP authentication
	 *
	 * @param string $table
	 * @return array
	 * @throws \RuntimeException
	 */
	protected function findEntitiesWithIpAuthentication($table) {
		$enableFields = EnableFieldsUtility::enableFields($table);
		$entities = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,pid', $table, 'tx_aoeipauth_ip > 0' . $enableFields);

		if (empty($entities)) {
			return array();
		}

		// Enrich with IPs
		$finalEntities = array();

		foreach ($entities as $entity) {
			$uid = $entity['uid'];
			if (self::TABLE_GROUP == $table) {
				$matchedIps = $this->getIpService()->findIpsByFeGroupId($uid);
			} elseif (self::TABLE_USER == $table) {
				$matchedIps = $this->getIpService()->findIpsByFeUserId($uid);
			} else {
				throw new \RuntimeException('Cannot load entries for unknown table.', 1390299890);
			}

			// Skip groups that do not find a corresponding ip
			if (empty($matchedIps)) {
				continue;
			}
			// Inject the matched ips to the group
			$entity['tx_aoeipauth_ip'] = $matchedIps;
			$finalEntities[] = $entity;
		}

		return $finalEntities;
	}

	/**
	 * @return \AOE\AoeIpauth\Domain\Service\IpService
	 */
	protected function getIpService() {
		if (NULL === $this->ipService) {
			$this->ipService = GeneralUtility::makeInstance('AOE\\AoeIpauth\\Domain\\Service\\IpService');
		}
		return $this->ipService;
	}

	/**
	 * @return \AOE\AoeIpauth\Service\IpMatchingService
	 */
	protected function getIpMatchingService() {
		if (NULL === $this->ipMatchingService) {
			$this->ipMatchingService = GeneralUtility::makeInstance('AOE\\AoeIpauth\\Service\\IpMatchingService');
		}
		return $this->ipMatchingService;
	}
}