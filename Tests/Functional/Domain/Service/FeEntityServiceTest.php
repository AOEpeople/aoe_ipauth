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
 * Class FeEntityServiceTest
 *
 * @package AOE\AoeIpauth\Tests\Functional\Domain\Service
 */
class FeEntityServiceTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase
{

    /**
     * @var \AOE\AoeIpauth\Domain\Service\FeEntityService
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
        $this->fixture = $this->objectManager->get('AOE\\AoeIpauth\\Domain\\Service\\FeEntityService');
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
    // Tests concerning findAllGroupsWithIpAuthentication
    ///////////////////////////

    /**
     * @test
     */
    public function findAllGroupsWithIpAuthenticationFindsCorrectFeGroups()
    {
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
    public function findAllUsersWithIpAuthenticationFindsCorrectFeUsers()
    {
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
    public static function findAllGroupsAuthenticatedByIpGetsCorrectGroupsProvider()
    {
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
    public function findAllGroupsAuthenticatedByIpGetsCorrectGroups($ip, $knownGroups, $finalGroupArray)
    {
        $stubbedFixture = $this->getMock('AOE\\AoeIpauth\\Domain\\Service\\FeEntityService', array('findEntitiesWithIpAuthentication'));

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
    public static function findAllUsersAuthenticatedByIpGetsCorrectUsersProvider()
    {
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
    public function findAllUsersAuthenticatedByIpGetsCorrectUsers($ip, $knownUsers, $finalUserArray)
    {
        $stubbedFixture = $this->getMock('AOE\\AoeIpauth\\Domain\\Service\\FeEntityService', array('findEntitiesWithIpAuthentication'));

        $stubbedFixture
            ->expects($this->any())
            ->method('findEntitiesWithIpAuthentication')
            ->will($this->returnValue($knownUsers));

        $users = $stubbedFixture->findAllUsersAuthenticatedByIp($ip);

        $this->assertSame($users, $finalUserArray);
    }
}
