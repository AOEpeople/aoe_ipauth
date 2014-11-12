<?php
namespace AOE\AoeIpauth\Tests\Functional\Domain\Service;

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
 * Class ContentServiceTest
 *
 * @package AOE\AoeIpauth\Tests\Functional\Domain\Service
 */
class ContentServiceTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase {

	/**
	 * @var \AOE\AoeIpauth\Domain\Service\ContentService
	 */
	protected $fixture;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * SetUp
	 */
	public function setUp() {
		$this->testExtensionsToLoad = array(
			'typo3conf/ext/aoe_ipauth',
		);
		parent::setUp();

		$this->fixturePath = __DIR__ . '/Fixtures/';

		/** @var $this->objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->fixture = $this->objectManager->get('AOE\\AoeIpauth\\Domain\\Service\\ContentService');
	}

	/**
	 * TearDown
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
				'pid' => 1,
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
				'pid' => 1,
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