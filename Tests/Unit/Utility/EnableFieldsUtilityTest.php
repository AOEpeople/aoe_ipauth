<?php
namespace AOE\AoeIpauth\Tests\Unit\Utility;

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

use AOE\AoeIpauth\Utility\EnableFieldsUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class EnableFieldsUtilityTest
 *
 * @package AOE\AoeIpauth\Tests\Unit\Utility
 */
class EnableFieldsUtilityTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @test
	 * @dataProvider enableFieldsUtilityReturnsRespectedEnableFieldsForTableDataProvider
	 */
	public function enableFieldsUtilityReturnsRespectedEnableFieldsForTable($expected, $table) {
		$this->assertSame($expected, EnableFieldsUtility::enableFields($table));
	}

	/**
	 * Data Provider for
	 * enableFieldsUtilityReturnsRespectedEnableFieldsOnTableInput
	 *
	 * @return array
	 */
	public static function enableFieldsUtilityReturnsRespectedEnableFieldsForTableDataProvider() {
		return array(
			'Table: fe_groups' => array(
				' AND hidden = 0 AND deleted = 0 ',
				'fe_groups'
			),
			'Table: fe_users' => array(
				' AND disable = 0 AND deleted = 0 ',
				'fe_users'
			),
			'Table: tt_content' => array(
				' AND hidden = 0 AND deleted = 0 ',
				'tt_content'
			),
			'Table: tx_aoeipauth_domain_model_ip' => array(
				' AND hidden = 0 AND deleted = 0 ',
				'tx_aoeipauth_domain_model_ip'
			)
		);
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function enableFieldsUtilityThrowsException() {
		EnableFieldsUtility::enableFields('UnknownTable');
	}
}