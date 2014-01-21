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
class Tx_AoeIpauth_Tests_Unit_Domain_Service_FeEntityServiceTest extends Tx_AoeIpauth_Tests_Unit_BaseDatabaseTest {

	/**
	 * @var Tx_AoeIpauth_Domain_Service_FeEntityService
	 */
	protected $fixture;

	/**
	 *
	 */
	public function setUp() {
		$this->fixture = $this->objectManager->get('Tx_AoeIpauth_Domain_Service_FeEntityService');
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
	// Tests concerning findAllGroupsWithIpAuthentication
	///////////////////////////

	/**
	 * @test
	 */
	public function findAllGroupsWithIpAuthenticationFindsCorrectFeGroups() {

		$this->importDataSet($this->fixturePath . 'DbDefaultFeGroups.xml');
		$this->importDataSet($this->fixturePath . 'DbDefaultTxAoeIpauthDomainModelIp.xml');

		$groups = $this->fixture->findAllGroupsWithIpAuthentication();
		$expectedGroups = array(
			array(
				'uid' => 1,
				'pid' => 1,
				'tx_aoeipauth_ip' => array('1.2.3.4'),
			),
		);

		$this->assertEquals($groups, $expectedGroups);
	}

	///////////////////////////
	// Tests concerning findAllUsersWithIpAuthentication
	///////////////////////////

	/**
	 * @test
	 */
	public function findAllUsersWithIpAuthenticationFindsCorrectFeUsers() {

		$this->importDataSet($this->fixturePath . 'DbDefaultFeUsers.xml');
		$this->importDataSet($this->fixturePath . 'DbDefaultTxAoeIpauthDomainModelIp.xml');

		$groups = $this->fixture->findAllUsersWithIpAuthentication();
		$expectedGroups = array(
			array(
				'uid' => 1,
				'pid' => 1,
				'tx_aoeipauth_ip' => array('5.6.7.8'),
			),
		);

		$this->assertEquals($groups, $expectedGroups);
	}

	///////////////////////////
	// Tests concerning findAllGroupsAuthenticatedByIp
	///////////////////////////


	/**
	 * Data Provider for findAllGroupsAuthenticatedByIpGetsCorrectGroups
	 *
	 * @return array
	 */
	public static function findAllGroupsAuthenticatedByIpGetsCorrectGroupsProvider() {
		return array(
			'whitelisted simple ip' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
						'tx_aoeipauth_ip' => array('192.168.1.200')
					)
				),
				array(
					1 => array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
					)
				)
			),
			'non-whitelisted simple ip' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
						'tx_aoeipauth_ip' => array('192.168.1.201')
					)
				),
				array()
			),
			'whitelisted simple wildcard' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
						'tx_aoeipauth_ip' => array('192.168.1.*')
					)
				),
				array(
					1 => array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
					)
				)
			),
			'non-whitelisted simple wildcard' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
						'tx_aoeipauth_ip' => array('192.168.2.*')
					)
				),
				array()
			),
			'whitelisted simple dash range' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
						'tx_aoeipauth_ip' => array('192.168.1.1-192.168.1.201')
					)
				),
				array(
					1 => array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
					)
				)
			),
			'non-whitelisted simple dash range' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
						'tx_aoeipauth_ip' => array('192.168.1.1-192.168.1.199')
					)
				),
				array()
			),
			'whitelisted simple cidr range' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
						'tx_aoeipauth_ip' => array('192.168.1.0/24')
					)
				),
				array(
					1 => array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
					)
				)
			),
			'non-whitelisted simple cidr range' => array(
				'192.168.2.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
						'tx_aoeipauth_ip' => array('192.168.1.0/24')
					)
				),
				array()
			),
		);
	}

	/**
	 * @test
	 * @dataProvider findAllGroupsAuthenticatedByIpGetsCorrectGroupsProvider
	 */
	public function findAllGroupsAuthenticatedByIpGetsCorrectGroups($ip, $knownGroups, $finalGroupArray) {

		$stubbedFixture = $this->getMock('Tx_AoeIpauth_Domain_Service_FeEntityService', array('findEntitiesWithIpAuthentication'));

		$stubbedFixture
			->expects($this->any())
			->method('findEntitiesWithIpAuthentication')
			->will($this->returnValue($knownGroups));

		$groups = $stubbedFixture->findAllGroupsAuthenticatedByIp($ip);

		$this->assertSame($groups, $finalGroupArray);
	}

	///////////////////////////
	// Tests concerning findAllUsersAuthenticatedByIp
	///////////////////////////


	/**
	 * Data Provider for findAllUsersAuthenticatedByIpGetsCorrectUsers
	 *
	 * @return array
	 */
	public static function findAllUsersAuthenticatedByIpGetsCorrectUsersProvider() {
		return array(
			'whitelisted simple ip' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
						'tx_aoeipauth_ip' => array('192.168.1.200')
					)
				),
				array(
					1 => array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
					)
				)
			),
			'non-whitelisted simple ip' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
						'tx_aoeipauth_ip' => array('192.168.1.201')
					)
				),
				array()
			),
			'whitelisted simple wildcard' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
						'tx_aoeipauth_ip' => array('192.168.1.*')
					)
				),
				array(
					1 => array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
					)
				)
			),
			'non-whitelisted simple wildcard' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
						'tx_aoeipauth_ip' => array('192.168.2.*')
					)
				),
				array()
			),
			'whitelisted simple dash range' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
						'tx_aoeipauth_ip' => array('192.168.1.1-192.168.1.201')
					)
				),
				array(
					1 => array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
					)
				)
			),
			'non-whitelisted simple dash range' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
						'tx_aoeipauth_ip' => array('192.168.1.1-192.168.1.199')
					)
				),
				array()
			),
			'whitelisted simple cidr range' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
						'tx_aoeipauth_ip' => array('192.168.1.0/24')
					)
				),
				array(
					1 => array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
					)
				)
			),
			'non-whitelisted simple cidr range' => array(
				'192.168.2.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
						'tx_aoeipauth_ip' => array('192.168.1.0/24')
					)
				),
				array()
			),
		);
	}

	/**
	 * @test
	 * @dataProvider findAllUsersAuthenticatedByIpGetsCorrectUsersProvider
	 */
	public function findAllUsersAuthenticatedByIpGetsCorrectUsers($ip, $knownUsers, $finalUserArray) {

		$stubbedFixture = $this->getMock('Tx_AoeIpauth_Domain_Service_FeEntityService', array('findEntitiesWithIpAuthentication'));

		$stubbedFixture
			->expects($this->any())
			->method('findEntitiesWithIpAuthentication')
			->will($this->returnValue($knownUsers));

		$users = $stubbedFixture->findAllUsersAuthenticatedByIp($ip);

		$this->assertSame($users, $finalUserArray);
	}
}
?>