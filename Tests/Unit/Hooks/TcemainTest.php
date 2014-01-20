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
 * Test case for class Tx_AoeIpauth_Hooks_Tcemain.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage AOE IP Auth
 *
 * @author DEV <dev@aoemedia.de>
 */
class Tx_AoeIpauth_Tests_Unit_Hooks_TcemainTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_AoeIpauth_Hooks_Tcemain
	 */
	protected $fixture;

	/**
	 *
	 */
	public function setUp() {
		$stubFixture = $this->getMock('Tx_AoeIpauth_Hooks_Tcemain', array('addFlashMessage'));
		$stubFixture
			->expects($this->any())
			->method('addFlashMessage')
			->will($this->returnValue(NULL));

		$this->fixture = $stubFixture;
		parent::setUp();
	}

	/**
	 *
	 */
	public function tearDown() {
		unset($this->fixture);
		parent::tearDown();
	}

	///////////////////////////
	// Tests concerning processDatamap_postProcessFieldArray
	///////////////////////////

	/**
	 * Data Provider for processDatamapPostProcessFieldArrayRejectsInvalidIps
	 *
	 * @return array
	 */
	public static function processDatamapPostProcessFieldArrayRejectsInvalidIpsProvider() {
		return array(
			'invalid simple ip' => array(
				'192.168.1.200s'
			),
			'invalid ip with wildcard' => array(
				'234.119.2.s'
			),
			'invalid dash range' => array(
				'234.119.2.1-234.119.2.1000'
			),
			'invalid cidr' => array(
				'234.119.2.1/40'
			),
		);
	}

	/**
	 * @test
	 * @dataProvider processDatamapPostProcessFieldArrayRejectsInvalidIpsProvider
	 */
	public function processDatamapPostProcessFieldArrayRejectsInvalidIps($ip) {

		$status = '';
		$table = Tx_AoeIpauth_Hooks_Tcemain::IP_TABLE;
		$id = 0;
		$fieldArray = array(
			'ip' => $ip,
		);
		$pObj = NULL;

		$this->fixture->processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $pObj);

		$this->assertSame(array(), $fieldArray);
	}

	/**
	 * Data Provider for processDatamapPostProcessFieldArrayCorrectlySetsRangeTypeInFieldArray
	 *
	 * @return array
	 */
	public static function processDatamapPostProcessFieldArrayCorrectlySetsRangeTypeInFieldArrayProvider() {
		return array(
			'simple ip' => array(
				'192.168.1.200', Tx_AoeIpauth_Service_IpMatchingService::NORMAL_IP_TYPE
			),
			'ip with wildcard' => array(
				'234.119.2.*', Tx_AoeIpauth_Service_IpMatchingService::WILDCARD_IP_TYPE
			),
			'dash range' => array(
				'234.119.2.1-234.119.2.100', Tx_AoeIpauth_Service_IpMatchingService::DASHRANGE_IP_TYPE
			),
			'cidr' => array(
				'234.119.2.1/20', Tx_AoeIpauth_Service_IpMatchingService::CIDR_IP_TYPE
			),
		);
	}

	/**
	 * @test
	 * @dataProvider processDatamapPostProcessFieldArrayCorrectlySetsRangeTypeInFieldArrayProvider
	 */
	public function processDatamapPostProcessFieldArrayCorrectlySetsRangeTypeInFieldArray($ip, $expected) {

		$status = '';
		$table = Tx_AoeIpauth_Hooks_Tcemain::IP_TABLE;
		$id = 0;
		$fieldArray = array(
			'ip' => $ip,
		);
		$pObj = NULL;

		$this->fixture->processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $pObj);

		$expectedFieldArray = array(
			'ip' => $ip,
			'range_type' => $expected,
		);
		$this->assertSame($expectedFieldArray, $fieldArray);
	}
}
?>