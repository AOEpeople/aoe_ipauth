<?php
namespace AOE\AoeIpauth\Tests\Functional\Domain\Service;

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

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IpServiceTest
 *
 * @package AOE\AoeIpauth\Tests\Functional\Domain\Service
 */
class IpServiceTest extends FunctionalTestCase
{

    /**
     * @var \AOE\AoeIpauth\Domain\Service\IpService
     */
    protected $fixture;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     *
     */
    public function setUp()
    {
        $this->testExtensionsToLoad = array(
            'typo3conf/ext/aoe_ipauth',
        );
        parent::setUp();

        $this->fixturePath = __DIR__ . '/Fixtures/';

        /** @var $this->objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        $this->fixture = $this->objectManager->get('AOE\\AoeIpauth\\Domain\\Service\\IpService');
    }

    /**
     *
     */
    public function tearDown()
    {
        unset($this->fixture);
        parent::tearDown();
    }

    ///////////////////////////
    // Tests concerning findIpsByFeGroupId
    ///////////////////////////

    /**
     * @test
     */
    public function findIpsByFeGroupIdFindsCorrectIps()
    {
        $this->importDataSet($this->fixturePath . 'DbDefaultTxAoeIpauthDomainModelIp.xml');

        $ips = $this->fixture->findIpsByFeGroupId(1);
        $expectedIps = array('1.2.3.4');

        $this->assertEquals($ips, $expectedIps);
    }

    ///////////////////////////
    // Tests concerning findIpsByFeUserId
    ///////////////////////////

    /**
     * @test
     */
    public function findIpsByFeUserIdFindsCorrectIps()
    {
        $this->importDataSet($this->fixturePath . 'DbDefaultTxAoeIpauthDomainModelIp.xml');

        $ips = $this->fixture->findIpsByFeUserId(1);
        $expectedIps = array('5.6.7.8');

        $this->assertEquals($ips, $expectedIps);
    }
}
