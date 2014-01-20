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
 * Test case for class Tx_AoeIpauth_Hooks_Staticfilecache.
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
class Tx_AoeIpauth_Tests_Unit_Hooks_StaticfilecacheTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_AoeIpauth_Hooks_Staticfilecache
	 */
	protected $fixture;

	/**
	 *
	 */
	public function setUp() {
		$this->fixture = $this->objectManager->get('Tx_AoeIpauth_Hooks_Staticfilecache');
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
	// Tests concerning createFileInitializeVariables
	///////////////////////////

	/**
	 * @test
	 */
	public function createFileInitializeVariablesDeniesCachingForUserCustomizedPages() {

		$tsfe = new stdClass();
		$tsfe->tmpl = new stdClass();
		$tsfe->tmpl->setup = array(
			'config.' => array(
				'aoe_ipauth.' => array(
					'staticAwareness' => 1,
				)
			)
		);

		$ncFixture = $this->getMock('tx_ncstaticfilecache');

		$stubFixture = $this->getMock('Tx_AoeIpauth_Hooks_Staticfilecache', array('isPageUserCustomized'));
		$stubFixture
			->expects($this->any())
			->method('isPageUserCustomized')
			->will($this->returnValue(TRUE));

		$parameters = array(
			'TSFE' => $tsfe,
			'staticCacheable' => TRUE
		);

		$stubFixture->createFileInitializeVariables($parameters, $ncFixture);

		$this->assertSame($parameters['staticCacheable'], FALSE);
	}

	/**
	 * @test
	 */
	public function createFileInitializeVariablesAllowsCachingForUncustomizedPages() {

		// No static awareness enabled
		$tsfe = new stdClass();
		$tsfe->tmpl = new stdClass();
		$tsfe->tmpl->setup = array(
			'config.' => array(
				'aoe_ipauth.' => array(
					'staticAwareness' => 1,
				)
			)
		);

		$ncFixture = $this->getMock('tx_ncstaticfilecache');

		$stubFixture = $this->getMock('Tx_AoeIpauth_Hooks_Staticfilecache', array('isPageUserCustomized'));
		$stubFixture
			->expects($this->any())
			->method('isPageUserCustomized')
			->will($this->returnValue(FALSE));

		$parameters = array(
			'TSFE' => $tsfe,
			'staticCacheable' => TRUE
		);

		$stubFixture->createFileInitializeVariables($parameters, $ncFixture);

		$this->assertSame($parameters['staticCacheable'], TRUE);
	}

	/**
	 * @test
	 */
	public function createFileInitializeVariablesSkipsCheckIfStaticAwarenessIsDisabled() {

		// No static awareness enabled
		$tsfe = new stdClass();
		$tsfe->tmpl = new stdClass();
		$tsfe->tmpl->setup = array(
			'config.' => array(
				'aoe_ipauth.' => array(
					'staticAwareness' => 0,
				)
			)
		);

		$ncFixture = $this->getMock('tx_ncstaticfilecache');

		$stubFixture = $this->getMock('Tx_AoeIpauth_Hooks_Staticfilecache', array('isPageUserCustomized'));
		$stubFixture
			->expects($this->any())
			->method('isPageUserCustomized')
			->will($this->returnValue(TRUE));

		$parameters = array(
			'TSFE' => $tsfe,
			'staticCacheable' => TRUE
		);

		$stubFixture->createFileInitializeVariables($parameters, $ncFixture);

		$this->assertSame($parameters['staticCacheable'], TRUE);
	}

	/**
	 * @test
	 */
	public function createFileInitializeVariablesSkipsCheckIfPageIsAlreadyNotStaticallyCachable() {

		// No static awareness enabled
		$tsfe = new stdClass();
		$tsfe->tmpl = new stdClass();
		$tsfe->tmpl->setup = array(
			'config.' => array(
				'aoe_ipauth.' => array(
					'staticAwareness' => 1,
				)
			)
		);

		$ncFixture = $this->getMock('tx_ncstaticfilecache');

		$stubFixture = $this->getMock('Tx_AoeIpauth_Hooks_Staticfilecache', array('isPageUserCustomized'));
		$stubFixture
			->expects($this->any())
			->method('isPageUserCustomized')
			->will($this->returnValue(FALSE));

		$parameters = array(
			'TSFE' => $tsfe,
			'staticCacheable' => FALSE
		);

		$stubFixture->createFileInitializeVariables($parameters, $ncFixture);

		$this->assertSame($parameters['staticCacheable'], FALSE);
	}
}
?>