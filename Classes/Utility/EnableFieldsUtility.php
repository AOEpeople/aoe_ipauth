<?php
namespace AOE\AoeIpauth\Utility;

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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Class EnableFieldsUtility
 *
 * @package AOE\AoeIpauth\Utility
 */
class EnableFieldsUtility
{

    /**
     * @param string $table
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function enableFields($table)
    {
        $enableFields = array(
            'fe_groups' => ' AND hidden = 0 AND deleted = 0 ',
            'fe_users' => ' AND disable = 0 AND deleted = 0 ',
            'tt_content' => ' AND hidden = 0 AND deleted = 0 ',
            'tx_aoeipauth_domain_model_ip' => ' AND hidden = 0 AND deleted = 0 '
        );

        if (!isset($enableFields[$table])) {
            throw new \InvalidArgumentException('Table: ' . $table . 'is not supported in this function');
        }

        return $enableFields[$table];
    }
}
