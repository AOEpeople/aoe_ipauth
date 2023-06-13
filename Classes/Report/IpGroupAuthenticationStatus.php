<?php
namespace AOE\AoeIpauth\Report;

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
use TYPO3\CMS\Reports\StatusProviderInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use AOE\AoeIpauth\Domain\Service\FeEntityService;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IpGroupAuthenticationStatus
 *
 * @package AOE\AoeIpauth\Report
 */
class IpGroupAuthenticationStatus implements StatusProviderInterface
{

    /**
     * @var ObjectManager
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
    public function getStatus()
    {
        $reports = array();

        // create object manager
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->objectManager = clone $objectManager;

        $this->myIp = GeneralUtility::getIndpEnv('REMOTE_ADDR');

        $this->analyseUserGroups($reports);

        return $reports;
    }

    /**
     * Analyses user groups
     *
     * @param array $reports
     * @return void
     */
    protected function analyseUserGroups(&$reports)
    {
        /** @var FeEntityService $service */
        $service = $this->objectManager->get('AOE\\AoeIpauth\\Domain\\Service\\FeEntityService');

        $userGroups = $service->findAllGroupsWithIpAuthentication();

        if (empty($userGroups)) {
            // Message that no user group has IP authentication
            $reports[] = $this->objectManager->get(
                'TYPO3\\CMS\\Reports\\Status',
                'IP Usergroup Authentication',
                'No user groups with IP authentication found',
                'No user groups were found anywhere that are active and have an automatic IP authentication enabled.' .
                    'Your current IP is: <strong>' . $this->myIp . '</strong>',
                Status::INFO
            );
        } else {
            $thisUrl = urlencode(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));

            $userGroupInfo = '<br /><br /><table cellpadding="4" cellspacing="0" border="0">';
            $userGroupInfo .= '<thead><tr><th style="padding-bottom: 10px;">User Group</th><th>IP/Range</th></tr></thead>';
            $userGroupInfo .= '<tbody>';

            foreach ($userGroups as $group) {
                $uid = $group['uid'];
                $ips = implode(', ', $group['tx_aoeipauth_ip']);

                $fullRecord = BackendUtility::getRecord('fe_groups', $uid);
                $title = $fullRecord['title'];

                $button = '<a title="Edit record" onclick="window.location.href=\'alt_doc.php?returnUrl=' . $thisUrl .
                            '&amp;edit[fe_groups][' . $uid . ']=edit\'; return false;" href="#">' .
                            '<span class="t3-icon t3-icon-actions t3-icon-actions-document t3-icon-document-open">&nbsp;</span>' .
                            '</a>';

                $userGroupInfo .= '<tr><td style="padding: 0 20px 0 0;">' . $button . $title . '</td><td>' . $ips . '</td></tr>';
            }

            $userGroupInfo .= '</tbody>';
            $userGroupInfo .= '</table>';

            $userGroupInfo .= '<br /><br />Your current IP is: <strong>' . $this->myIp . '</strong>';

            $reports[] = $this->objectManager->get('TYPO3\\CMS\\Reports\\Status',
                'IP Usergroup Authentication',
                'Some groups with automatic IP authentication were found.',
                $userGroupInfo,
                Status::OK
            );
        }
    }
}
