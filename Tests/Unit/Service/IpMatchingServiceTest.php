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
 * Test case for class Tx_AoeIpauth_Service_IpMatchingService.
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
class Tx_AoeIpauth_Tests_Unit_Service_IpMatchingServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_AoeIpauth_Service_IpMatchingService
	 */
	protected $fixture;

	/**
	 *
	 */
	public function setUp() {
		$this->fixture = $this->objectManager->get('Tx_AoeIpauth_Service_IpMatchingService');
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
	// Tests concerning isValidIp
	///////////////////////////

	/**
	 * Data Provider for isValidIpDeterminesIpValidityCorrectly
	 *
	 * @return array
	 */
	public static function isValidIpDeterminesIpValidityCorrectlyProvider() {
		return array(
			'valid simple ip' => array(
				'192.168.1.200', TRUE
			),
			'invalid simple ip (third touple exceeds 255)' => array(
				'234.119.260.65', FALSE
			),
			'invalid simple ip (not enough touples)' => array(
				'234.119.1', FALSE
			),
			'invalid simple ip (not enough touples, but enough dots)' => array(
				'234.119.1.', FALSE
			),
			'invalid simple ip (string at end)' => array(
				'234.119.1.x', FALSE
			),
			'invalid simple ip (string in middle)' => array(
				'234.119.x.1', FALSE
			),
			'invalid simple ip (negative number)' => array(
				'-1.119.1.1', FALSE
			),
			'invalid simple ip (cidr)' => array(
				'234.119.2.1/20', FALSE
			),
			'invalid simple ip (wildcard)' => array(
				'234.119.2.*', FALSE
			),
			'invalid simple ip (dash range)' => array(
				'234.119.2.1-234.119.2.10', FALSE
			),
		);
	}

	/**
	 * @test
	 * @dataProvider isValidIpDeterminesIpValidityCorrectlyProvider
	 */
	public function isValidIpDeterminesIpValidityCorrectly($ip, $expected) {
		$this->assertSame($this->fixture->isValidIp($ip), $expected);
	}

	///////////////////////////
	// Tests concerning isValidWildcardIp
	///////////////////////////

	/**
	 * Data Provider for isValidWildcardIpDeterminesIpValidityCorrectly
	 *
	 * @return array
	 */
	public static function isValidWildcardIpDeterminesIpValidityCorrectlyProvider() {
		return array(
			'valid simple wildcard' => array(
				'192.168.1.*', TRUE
			),
			'valid simple wildcard (wildcard in middle)' => array(
				'192.*.1.1', TRUE
			),
			'valid simple wildcard (wildcard in front)' => array(
				'*.1.1.1', TRUE
			),
			'valid simple wildcard (multiple wildcards)' => array(
				'1.*.*.*', TRUE
			),
			'valid simple wildcard (all wildcards)' => array(
				'*.*.*.*', TRUE
			),
			'invalid simple wildcard (too many wildcards)' => array(
				'*.*.*.*.*', FALSE
			),
			'invalid simple wildcard (cidr)' => array(
				'234.119.2.1/20', FALSE
			),
			'invalid simple wildcard (normal ip)' => array(
				'234.119.2.1', FALSE
			),
			'invalid simple wildcard (dash range)' => array(
				'234.119.2.1-234.119.2.10', FALSE
			),
		);
	}

	/**
	 * @test
	 * @dataProvider isValidWildcardIpDeterminesIpValidityCorrectlyProvider
	 */
	public function isValidWildcardIpDeterminesIpValidityCorrectly($ip, $expected) {
		$this->assertSame($this->fixture->isValidWildcardIp($ip), $expected);
	}

	///////////////////////////
	// Tests concerning isValidDashRange
	///////////////////////////

	/**
	 * Data Provider for isValidDashRangeDeterminesIpValidityCorrectly
	 *
	 * @return array
	 */
	public static function isValidDashRangeDeterminesIpValidityCorrectlyProvider() {
		return array(
			'valid simple dash range' => array(
				'234.119.2.1-234.119.2.10', TRUE
			),
			'invalid simple dash range (left ip is invalid)' => array(
				'234.119.260.1-234.119.2.10', FALSE
			),
			'invalid simple dash range (right ip is invalid)' => array(
				'234.119.2.1-234.119.260.1', FALSE
			),
			'invalid simple ip (cidr)' => array(
				'234.119.2.1/20', FALSE
			),
			'invalid simple ip (wildcard)' => array(
				'234.119.2.*', FALSE
			),
			'invalid simple ip (' => array(
				'234.119.2.1', FALSE
			),
		);
	}

	/**
	 * @test
	 * @dataProvider isValidDashRangeDeterminesIpValidityCorrectlyProvider
	 */
	public function isValidDashRangeDeterminesIpValidityCorrectly($ip, $expected) {
		$this->assertSame($this->fixture->isValidDashRange($ip), $expected);
	}

	///////////////////////////
	// Tests concerning isValidCidrRange
	///////////////////////////

	/**
	 * Data Provider for isValidCidrRangeDeterminesIpValidityCorrectly
	 *
	 * @return array
	 */
	public static function isValidCidrRangeDeterminesIpValidityCorrectlyProvider() {
		return array(
			'valid simple cidr range' => array(
				'234.119.2.1/20', TRUE
			),
			'invalid simple cidr range (suffix illegal)' => array(
				'234.119.2.1/0.5', FALSE
			),
			'invalid simple cidr range (suffix illegal #2)' => array(
				'234.119.2.1/-1', FALSE
			),
			'invalid simple cidr range (suffix too big)' => array(
				'234.119.2.1/50', FALSE
			),
			'invalid simple cidr range (ip wrong)' => array(
				'234.119.270.1/16', FALSE
			),
			'invalid simple ip (simple ip)' => array(
				'234.119.2.1', FALSE
			),
			'invalid simple ip (wildcard)' => array(
				'234.119.2.*', FALSE
			),
			'invalid simple ip (dash range)' => array(
				'234.119.2.1-234.119.2.10', FALSE
			),
		);
	}

	/**
	 * @test
	 * @dataProvider isValidCidrRangeDeterminesIpValidityCorrectlyProvider
	 */
	public function isValidCidrRangeDeterminesIpValidityCorrectly($ip, $expected) {
		$this->assertSame($this->fixture->isValidCidrRange($ip), $expected);
	}

	///////////////////////////
	// Tests concerning isIpAllowed
	///////////////////////////

	/**
	 * Data Provider for isIpAllowedDeterminesIpValidityCorrectly
	 *
	 * @return array
	 */
	public static function isIpAllowedDeterminesIpValidityCorrectlyProvider() {
		return array(
			'whitelisted simple ip' => array(
				'234.119.2.1', '234.119.2.1', TRUE
			),
			'non-whitelisted simple ip' => array(
				'234.119.2.1', '234.119.2.2', FALSE
			),
			'whitelisted wildcard ip' => array(
				'234.119.2.1', '234.119.2.*', TRUE
			),
			'non-whitelisted wildcard ip' => array(
				'234.119.3.1', '234.119.2.*', FALSE
			),
			'whitelisted dash range' => array(
				'234.119.2.3', '234.119.2.1-234.119.2.10', TRUE
			),
			'whitelisted dash range (lower corner case)' => array(
				'234.119.2.1', '234.119.2.1-234.119.2.10', TRUE
			),
			'whitelisted dash range (upper corner case)' => array(
				'234.119.2.10', '234.119.2.1-234.119.2.10', TRUE
			),
			'non-whitelisted dash range' => array(
				'234.119.3.3', '234.119.2.4-234.119.2.9', FALSE
			),
			'non-whitelisted dash range (lower corner case)' => array(
				'234.119.2.1', '234.119.2.2-234.119.2.10', FALSE
			),
			'non-whitelisted dash range (upper corner case)' => array(
				'234.119.2.10', '234.119.2.1-234.119.2.9', FALSE
			),
			'whitelisted cidr range (low end)' => array(
				'234.119.2.1', '234.119.2.0/24', TRUE
			),
			'whitelisted cidr range (high end)' => array(
				'234.119.2.254', '234.119.2.0/24', TRUE
			),
			'non-whitelisted cidr range (first touple)' => array(
				'233.119.2.3', '235.119.2.0/24', FALSE
			),
			'non-whitelisted cidr range (second touple)' => array(
				'235.120.2.3', '235.119.2.0/24', FALSE
			),
			'non-whitelisted cidr range (third touple)' => array(
				'235.119.3.3', '235.119.2.0/24', FALSE
			),
		);
	}

	/**
	 * @test
	 * @dataProvider isIpAllowedDeterminesIpValidityCorrectlyProvider
	 */
	public function isIpAllowedDeterminesIpValidityCorrectly($givenIp, $whitelist, $expected) {
		$this->assertSame($this->fixture->isIpAllowed($givenIp, $whitelist), $expected);
	}
}
?>