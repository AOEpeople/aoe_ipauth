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
class Tx_AoeIpauth__Report_IpUserAuthenticationStatus implements tx_reports_StatusProvider {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface The object manager
	 */
	protected $objectManager;

	/**
	 * @var string
	 */
	protected $myIp;

	/**
	 *
	 * @see typo3/sysext/reports/interfaces/tx_reports_StatusProvider::getStatus()
	 */
	public function getStatus() {

		$reports = array();

		// create object manager
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->objectManager = $objectManager;

		$this->myIp = t3lib_div::getIndpEnv('REMOTE_ADDR');

		$this->analyseUses($reports);

		return $reports;
	}

	/**
	 * Analyses users
	 *
	 * @param array $reports
	 */
	protected function analyseUses(&$reports) {

		/** @var Tx_AoeIpauth_Domain_Service_FeEntityService $service */
		$service = $this->objectManager->get('Tx_AoeIpauth_Domain_Service_FeEntityService');

		$users = $service->findAllUsersWithIpAuthentication();

		if (empty($users)) {
			// Message that no user group has IP authentication
			$reports[] = $this->objectManager->get('tx_reports_reports_status_Status',
				'IP User Authentication',
				'No users with IP authentication found',
				'No users were found anywhere that are active and have an automatic IP authentication enabled. Your current IP is: <strong>' . $this->myIp . '</strong>',
				tx_reports_reports_status_Status::INFO
			);
		} else {

			$thisUrl = urlencode(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));

			$userInfo = '<br /><br /><table cellpadding="4" cellspacing="0" border="0">';
			$userInfo .= '<thead><tr><th style="padding-bottom: 10px;">User</th><th>IP/Range</th></tr></thead>';
			$userInfo .= '<tbody>';

			// Add user group strings
			foreach ($users as $user) {
				$uid = $user['uid'];
				$ips = implode (', ', $user['tx_aoeipauth_ip']);

				$fullRecord = t3lib_BEfunc::getRecord('fe_users', $uid);
				$title = $fullRecord['username'];


				$button = '<a title="Edit record" onclick="window.location.href=\'alt_doc.php?returnUrl=' . $thisUrl . '&amp;edit[fe_users][' . $uid . ']=edit\'; return false;" href="#">' .
							'<span class="t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-open">&nbsp;</span>' .
						  '</a>';

				$userInfo .= '<tr><td style="padding: 0 20px 0 0;">' .  $button . $title . '</td><td>' . $ips . '</td></tr>';
			}

			$userInfo .= '</tbody>';
			$userInfo .= '</table>';

			$userInfo .= '<br /><br />Your current IP is: <strong>' . $this->myIp . '</strong>';

			// Inform about the groups
			$reports[] = $this->objectManager->get('tx_reports_reports_status_Status',
				'IP User Authentication',
				'Some users with automatic IP authentication were found.',
				$userInfo,
				tx_reports_reports_status_Status::OK
			);
		}
	}
}
?>