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
class Tx_AoeIpauth_Tests_Unit_Domain_Service_ContentServiceTest extends Tx_AoeIpauth_Tests_Unit_BaseDatabaseTest {

	/**
	 * @var Tx_AoeIpauth_Domain_Service_ContentService
	 */
	protected $fixture;

	/**
	 *
	 */
	public function setUp() {
		$this->fixture = $this->objectManager->get('Tx_AoeIpauth_Domain_Service_ContentService');
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
	// Tests concerning isPageUserCustomized
	///////////////////////////

	/**
	 * @test
	 */
	public function isPageUserCustomizedWorks() {

		$this->importDataSet($this->fixturePath . 'DbDefaultTtContent.xml');
		$language = 0;

		$isCustomized = $this->fixture->isPageUserCustomized(1, $language);
		$this->assertTrue($isCustomized);

		$isCustomized = $this->fixture->isPageUserCustomized(2, $language);
		$this->assertFalse($isCustomized);

		$isCustomized = $this->fixture->isPageUserCustomized(3, $language);
		$this->assertFalse($isCustomized);

		$isCustomized = $this->fixture->isPageUserCustomized(4, $language);
		$this->assertTrue($isCustomized);
	}

	/**
	 * @test
	 */
	public function isPageUserCustomizedWorksForOverlays() {

		$this->importDataSet($this->fixturePath . 'DbDefaultTtContent.xml');
		$language = 2;

		$isCustomized = $this->fixture->isPageUserCustomized(1, $language);
		$this->assertTrue($isCustomized);

		$isCustomized = $this->fixture->isPageUserCustomized(2, $language);
		$this->assertTrue($isCustomized);

		$isCustomized = $this->fixture->isPageUserCustomized(3, $language);
		$this->assertFalse($isCustomized);

		$isCustomized = $this->fixture->isPageUserCustomized(4, $language);
		$this->assertTrue($isCustomized);
	}

	///////////////////////////
	// Tests concerning findUserCustomizedContentByPageId
	///////////////////////////

	/**
	 * @test
	 */
	public function findUserCustomizedContentByPageIdWorks() {

		$this->importDataSet($this->fixturePath . 'DbDefaultTtContent.xml');
		$language = 0;

		$content = $this->fixture->findUserCustomizedContentByPageId(1, $language);
		$expectedContent = array(
			array(
				'uid' => 205,
				'pid'=> 1,
			)
		);

		$this->assertEquals($content, $expectedContent);
	}

	/**
	 * @test
	 */
	public function findUserCustomizedContentByPageIdWorksForOverlays() {

		$this->importDataSet($this->fixturePath . 'DbDefaultTtContent.xml');
		$language = 2;

		$content = $this->fixture->findUserCustomizedContentByPageId(1, $language);
		$expectedContent = array(
			array(
				'uid' => 210,
				'pid'=> 1,
			)
		);

		$this->assertEquals($content, $expectedContent);
	}

	///////////////////////////
	// Tests concerning isPageBareUserCustomized
	///////////////////////////

	/**
	 * @test
	 */
	public function isPageBareUserCustomizedWorks() {

		$this->importDataSet($this->fixturePath . 'DbDefaultTtContent.xml');
		$language = 0;

		$isPageBareCustomized = $this->fixture->isPageBareUserCustomized(1, $language);
		$this->assertFalse($isPageBareCustomized);

		$isPageBareCustomized = $this->fixture->isPageBareUserCustomized(4, $language);
		$this->assertTrue($isPageBareCustomized);

		$isPageBareCustomized = $this->fixture->isPageBareUserCustomized(5, $language);
		$this->assertTrue($isPageBareCustomized);
	}

	/**
	 * @test
	 */
	public function isPageBareUserCustomizedWorksForOverlays() {

		$this->importDataSet($this->fixturePath . 'DbDefaultTtContent.xml');
		$language = 2;

		$isPageBareCustomized = $this->fixture->isPageBareUserCustomized(1, $language);
		$this->assertFalse($isPageBareCustomized);

		$isPageBareCustomized = $this->fixture->isPageBareUserCustomized(5, $language);
		$this->assertTrue($isPageBareCustomized);
	}

}
?>