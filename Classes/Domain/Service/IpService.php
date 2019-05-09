<?php
namespace AOE\AoeIpauth\Domain\Service;

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

use AOE\AoeIpauth\Utility\EnableFieldsUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IpService
 *
 * @package AOE\AoeIpauth\Domain\Service
 */
class IpService implements  \TYPO3\CMS\Core\SingletonInterface
{

    const TABLE = 'tx_aoeipauth_domain_model_ip';

    /**
     * Returns all ip domain model records that belong to a given fe_user uid
     *
     * @param int $uid fe_users uid
     * @return array
     */
    public function findIpsByFeUserId($uid)
    {
        return $this->findIpsByField('fe_user', $uid);
    }

    /**
     * Returns all ip domain model records that belong to a given fe_groups uid
     *
     * @param int $uid fe_groups uid
     * @return array
     */
    public function findIpsByFeGroupId($uid)
    {
        return $this->findIpsByField('fe_group', $uid);
    }

    /**
     * Finds IPs from the table by a given field and field value
     *
     * @param string $field
     * @param int $value
     * @return array
     */
    protected function findIpsByField($field, $value)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->getRestrictions()->removeAll();
        $ips = $queryBuilder->select('ip')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq($field, (int)$value . ' ' . EnableFieldsUtility::enableFields(self::TABLE))
            )
            ->execute()
            ->fetchAll();

        if (empty($ips)) {
            return array();
        }
        $finalIps = array();
        foreach ($ips as $record) {
            $finalIps[] = $record['ip'];
        }

        return $finalIps;
    }
}
