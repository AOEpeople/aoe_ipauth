<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 DEV <dev@aoemedia.de>, AOE media GmbH
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
class Tx_AoeIpauth_Hooks_Tcemain {

	const IP_TABLE = 'tx_aoeipauth_domain_model_ip';

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface The object manager
	 */
	protected $objectManager;

	/**
	 * Post process
	 *
	 * @param string $status
	 * @param string $table
	 * @param string $id
	 * @param array $fieldArray
	 * @param t3lib_TCEmain $pObj
	 * @return void
	 */
	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$pObj) {

		if (self::IP_TABLE != $table || empty($fieldArray) || !isset($fieldArray['ip'])) {
			return;
		}

		/** @var Tx_AoeIpauth_Service_IpMatchingService $ipMatchingService */
		$ipMatchingService = $this->getObjectManager()->get('Tx_AoeIpauth_Service_IpMatchingService');

		$potentialIp = $fieldArray['ip'];

		// If it is a valid IP, return. No further action needed.
		$isValidIp = $ipMatchingService->isValidIp($potentialIp);
		if ($isValidIp) {
			$fieldArray['range_type'] = Tx_AoeIpauth_Service_IpMatchingService::NORMAL_IP_TYPE;
			return;
		}

		// Allow wildcard notations
		$isValidWildcard = $ipMatchingService->isValidWildcardIp($potentialIp);
		if ($isValidWildcard) {
			$fieldArray['range_type'] = Tx_AoeIpauth_Service_IpMatchingService::WILDCARD_IP_TYPE;
			return;
		}

		// Allow dash-range notations
		$isValidDashRange = $ipMatchingService->isValidDashRange($potentialIp);
		if ($isValidDashRange) {
			$fieldArray['range_type'] = Tx_AoeIpauth_Service_IpMatchingService::DASHRANGE_IP_TYPE;
			return;
		}

		// Check if it is a valid CIDR range
		$isValidRange = $ipMatchingService->isValidCidrRange($potentialIp);
		if ($isValidRange) {
			$fieldArray['range_type'] = Tx_AoeIpauth_Service_IpMatchingService::CIDR_IP_TYPE;
			return;
		}

		// Neither a valid IP nor a valid range
		unset($fieldArray['ip']);
		$this->addFlashMessage('The new IP (<strong>' . $potentialIp . '</strong>) you entered was neither a valid IP nor a valid range. The change was rejected.', t3lib_FlashMessage::ERROR);
	}


	/**
	 * Adds a simple flash message
	 *
	 * @param $message
	 * @param $code
	 * @return void
	 */
	protected function addFlashMessage($message, $code) {
		$flashMessage = t3lib_div::makeInstance('t3lib_FlashMessage',
			$message,
			'',
			$code,
			TRUE
		);
		t3lib_FlashMessageQueue::addMessage($flashMessage);
	}

	/**
	 * Gets the object manager
	 *
	 * @return Tx_Extbase_Object_ObjectManager
	 */
	protected function getObjectManager() {
		// create object manager
		if (!$this->objectManager) {
			$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
			$this->objectManager =  clone $objectManager;
		}
		return $this->objectManager;
	}

}
?>