<?php
namespace AOE\AoeIpauth\Tests\Functional\Hooks;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class StaticfilecacheTest
 *
 * @package AOE\AoeIpauth\Tests\Functional\Hooks
 */
class StaticfilecacheTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase {

	/**
	 * @var \AOE\AoeIpauth\Hooks\Staticfilecache
	 */
	protected $fixture;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 */
	public function setUp() {
		$this->testExtensionsToLoad = array(
			'typo3conf/ext/aoe_ipauth',
		);
		parent::setUp();

		/** @var $this->objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

		$this->fixture = $this->objectManager->get('AOE\\AoeIpauth\\Hooks\\Staticfilecache');
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
		$tsfe = new \stdClass();
		$tsfe->tmpl = new \stdClass();
		$tsfe->tmpl->setup = array(
			'config.' => array(
				'aoe_ipauth.' => array(
					'staticAwareness' => 1,
				)
			)
		);

		$ncFixture = $this->getMock('tx_ncstaticfilecache');

		$stubFixture = $this->getMock('AOE\\AoeIpauth\\Hooks\\Staticfilecache', array('isPageUserCustomized'));
		$stubFixture
			->expects($this->any())
			->method('isPageUserCustomized')
			->will($this->returnValue(TRUE));

		$parameters = array(
			'TSFE' => $tsfe,
			'staticCacheable' => TRUE
		);

		$stubFixture->createFileInitializeVariables($parameters, $ncFixture);

		$this->assertFalse($parameters['staticCacheable']);
	}

	/**
	 * @test
	 */
	public function createFileInitializeVariablesAllowsCachingForUncustomizedPages() {
		// No static awareness enabled
		$tsfe = new \stdClass();
		$tsfe->tmpl = new \stdClass();
		$tsfe->tmpl->setup = array(
			'config.' => array(
				'aoe_ipauth.' => array(
					'staticAwareness' => 1,
				)
			)
		);

		$ncFixture = $this->getMock('tx_ncstaticfilecache');

		$stubFixture = $this->getMock('AOE\\AoeIpauth\\Hooks\\Staticfilecache', array('isPageUserCustomized'));
		$stubFixture
			->expects($this->any())
			->method('isPageUserCustomized')
			->will($this->returnValue(FALSE));

		$parameters = array(
			'TSFE' => $tsfe,
			'staticCacheable' => TRUE
		);

		$stubFixture->createFileInitializeVariables($parameters, $ncFixture);

		$this->assertTrue($parameters['staticCacheable']);
	}

	/**
	 * @test
	 */
	public function createFileInitializeVariablesSkipsCheckIfStaticAwarenessIsDisabled() {
		// No static awareness enabled
		$tsfe = new \stdClass();
		$tsfe->tmpl = new \stdClass();
		$tsfe->tmpl->setup = array(
			'config.' => array(
				'aoe_ipauth.' => array(
					'staticAwareness' => 0,
				)
			)
		);

		$ncFixture = $this->getMock('tx_ncstaticfilecache');

		$stubFixture = $this->getMock('AOE\\AoeIpauth\\Hooks\\Staticfilecache', array('isPageUserCustomized'));
		$stubFixture
			->expects($this->any())
			->method('isPageUserCustomized')
			->will($this->returnValue(TRUE));

		$parameters = array(
			'TSFE' => $tsfe,
			'staticCacheable' => TRUE
		);

		$stubFixture->createFileInitializeVariables($parameters, $ncFixture);

		$this->assertTrue($parameters['staticCacheable']);
	}

	/**
	 * @test
	 */
	public function createFileInitializeVariablesSkipsCheckIfPageIsAlreadyNotStaticallyCachable() {
		// No static awareness enabled
		$tsfe = new \stdClass();
		$tsfe->tmpl = new \stdClass();
		$tsfe->tmpl->setup = array(
			'config.' => array(
				'aoe_ipauth.' => array(
					'staticAwareness' => 1,
				)
			)
		);

		$ncFixture = $this->getMock('tx_ncstaticfilecache');

		$stubFixture = $this->getMock('AOE\\AoeIpauth\\Hooks\\Staticfilecache', array('isPageUserCustomized'));
		$stubFixture
			->expects($this->any())
			->method('isPageUserCustomized')
			->will($this->returnValue(FALSE));

		$parameters = array(
			'TSFE' => $tsfe,
			'staticCacheable' => FALSE
		);

		$stubFixture->createFileInitializeVariables($parameters, $ncFixture);

		$this->assertFalse($parameters['staticCacheable']);
	}
}
