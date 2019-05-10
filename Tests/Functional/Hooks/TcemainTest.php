<?php
namespace AOE\AoeIpauth\Tests\Functional\Hooks;

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

use AOE\AoeIpauth\Hooks\Tcemain;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;

/**
 * Class TcemainTest
 *
 * @package AOE\AoeIpauth\Tests\Functional\Hooks
 */
class TcemainTest extends FunctionalTestCase
{

    /**
     * @var \AOE\AoeIpauth\Hooks\Tcemain
     */
    protected $fixture;

    /**
     *
     */
    public function setUp()
    {
        $this->testExtensionsToLoad = array(
            'typo3conf/ext/aoe_ipauth',
        );
        parent::setUp();

        $stubFixture = $this->getAccessibleMock('AOE\\AoeIpauth\\Hooks\\Tcemain', array('addFlashMessage'));
        $stubFixture
            ->expects($this->any())
            ->method('addFlashMessage')
            ->will($this->returnValue(null));

        $this->fixture = $stubFixture;
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
    // Tests concerning processDatamap_postProcessFieldArray
    ///////////////////////////

    /**
     * Data Provider for
     * processDatamapPostProcessFieldArrayRejectsInvalidIps
     *
     * @return array
     */
    public static function processDatamapPostProcessFieldArrayRejectsInvalidIpsProvider()
    {
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
    public function processDatamapPostProcessFieldArrayRejectsInvalidIps($ip)
    {
        $status = '';
        $table = Tcemain::IP_TABLE;
        $id = 0;
        $fieldArray = array(
            'ip' => $ip,
        );
        $pObj = null;

        $this->fixture->processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $pObj);

        $this->assertSame(array(), $fieldArray);
    }

    /**
     * Data Provider for
     * processDatamapPostProcessFieldArrayCorrectlySetsRangeTypeInFieldArray
     *
     * @return array
     *
     * TODO: Figure out why i cannot use the
     * IpMatchingService::NORMAL_IP_TYPE directly.
     */
    public static function processDatamapPostProcessFieldArrayCorrectlySetsRangeTypeInFieldArrayProvider()
    {
        return array(
            'simple ip' => array(
                // \AOE\AoeIpauth\Service\IpMatchingService::NORMAL_IP_TYPE
                '192.168.1.200', 0
            ),
            'ip with wildcard' => array(
                // \AOE\AoeIpauth\Service\IpMatchingService::WILDCARD_IP_TYPE
                '234.119.2.*', 2
            ),
            'dash range' => array(
                // \AOE\AoeIpauth\Service\IpMatchingService::DASHRANGE_IP_TYPE
                '234.119.2.1-234.119.2.100', 3
            ),
            'cidr' => array(
                // \AOE\AoeIpauth\Service\IpMatchingService::CIDR_IP_TYPE
                '234.119.2.1/20', 1
            ),
        );
    }

    /**
     * @test
     * @dataProvider processDatamapPostProcessFieldArrayCorrectlySetsRangeTypeInFieldArrayProvider
     */
    public function processDatamapPostProcessFieldArrayCorrectlySetsRangeTypeInFieldArray($ip, $expected)
    {
        $status = '';
        $table = Tcemain::IP_TABLE;
        $id = 0;
        $fieldArray = array(
            'ip' => $ip,
        );
        $pObj = null;

        $this->fixture->processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $pObj);

        $expectedFieldArray = array(
            'ip' => $ip,
            'range_type' => $expected,
        );
        $this->assertSame($expectedFieldArray, $fieldArray);
    }
}
