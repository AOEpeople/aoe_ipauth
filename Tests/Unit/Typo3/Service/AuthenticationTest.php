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
class Tx_AoeIpauth_Tests_Unit_Typo3_Service_AuthenticationTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_AoeIpauth_Typo3_Service_Authentication
	 */
	protected $fixture;

	/**
	 *
	 */
	public function setUp() {
		$this->fixture = $this->objectManager->get('Tx_AoeIpauth_Typo3_Service_Authentication');
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
	// Tests concerning getUser
	///////////////////////////

	/**
	 * Data Provider for getUserAuthenticatesUser
	 *
	 * @return array
	 */
	public static function getUserAuthenticatesUserProvider() {
		return array(
			'one ip authenticated user' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
					)
				),
				array(
					'uid' => 1,
					'pid' => 1,
					'username' => 'Test User',
				),
			),
			'two ip authenticated users' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'username' => 'Test User',
					),
					array(
						'uid' => 2,
						'pid' => 1,
						'username' => 'Test User #2',
					)
				),
				array(
					'uid' => 2,
					'pid' => 1,
					'username' => 'Test User #2',
				),
			),
			'no ip authenticated users' => array(
				'192.168.1.200',
				array(),
				FALSE,
			),
		);
	}

	/**
	 * @test
	 * @dataProvider getUserAuthenticatesUserProvider
	 */
	public function getUserAuthenticatesUser($ip, $ipAuthenticatedUsers, $finalUserArray) {
		$stubbedFixture = $this->getMock('Tx_AoeIpauth_Typo3_Service_Authentication', array('findAllUsersByIpAuthentication'));

		$stubbedFixture
			->expects($this->any())
			->method('findAllUsersByIpAuthentication')
			->will($this->returnValue($ipAuthenticatedUsers));


		$stubbedFixture->mode = 'getUserFE';
		$stubbedFixture->authInfo = array(
			'REMOTE_ADDR' => $ip,
		);


		$user = $stubbedFixture->getUser();

		$this->assertEquals($user, $finalUserArray);
	}

	///////////////////////////
	// Tests concerning getGroups
	///////////////////////////

	/**
	 * Data Provider for getGroupsAuthenticatesGroups
	 *
	 * @return array
	 */
	public static function getGroupsAuthenticatesGroupsProvider() {
		return array(
			'one ip authenticated group' => array(
				'192.168.1.200',
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
					)
				),
				array(
					array(
						'uid' => 1,
						'pid' => 1,
						'title' => 'Test Group',
					),
					array(
						'uid' => 10,
						'pid' => 1,
						'title' => 'Test Group Existing',
					)
				)
			),
		);
	}

	/**
	 * @test
	 * @dataProvider getGroupsAuthenticatesGroupsProvider
	 */
	public function getGroupsAuthenticatesGroups($ip, $ipAuthenticatedGroups, $finalGroupArray) {

		$stubbedFixture = $this->getMock('Tx_AoeIpauth_Typo3_Service_Authentication', array('findAllGroupsByIpAuthentication'));

		$stubbedFixture
			->expects($this->any())
			->method('findAllGroupsByIpAuthentication')
			->will($this->returnValue($ipAuthenticatedGroups));


		$stubbedFixture->mode = 'getGroupsFE';
		$stubbedFixture->authInfo = array(
			'REMOTE_ADDR' => $ip,
		);
		$existingGroups = array(
			array(
				'uid' => 10,
				'pid' => 1,
				'title' => 'Test Group Existing',
			)
		);
		$groups = $stubbedFixture->getGroups('', $existingGroups);

		$this->assertEquals($groups, $finalGroupArray);
	}

	///////////////////////////
	// Tests concerning authUser
	///////////////////////////

	/**
	 * @test
	 */
	public function authUserAuthenticatesIpWhenUserIpMatches() {
		$stubbedFixture = $this->getMock('Tx_AoeIpauth_Typo3_Service_Authentication', array('doesCurrentUsersIpMatch'));

		$stubbedFixture
			->expects($this->any())
			->method('doesCurrentUsersIpMatch')
			->will($this->returnValue(TRUE));

		// Should work
		$stubbedFixture->authInfo = array(
			'loginType' => 'FE',
			'REMOTE_ADDR' => '1.2.3.4',
		);

		$user = $stubbedFixture->authUser(array('uid' => 1));
		$this->assertSame(200, $user);
	}

	/**
	 * @test
	 */
	public function authUserDoesNotAuthenticateWhenUserIpFails() {
		$stubbedFixture = $this->getMock('Tx_AoeIpauth_Typo3_Service_Authentication', array('doesCurrentUsersIpMatch'));

		$stubbedFixture
			->expects($this->any())
			->method('doesCurrentUsersIpMatch')
			->will($this->returnValue(FALSE));

		// Should work
		$stubbedFixture->authInfo = array(
			'loginType' => 'FE',
			'REMOTE_ADDR' => '1.2.3.4',
		);

		$user = $stubbedFixture->authUser(array('uid' => 1));
		$this->assertSame(100, $user);
	}

}
?>