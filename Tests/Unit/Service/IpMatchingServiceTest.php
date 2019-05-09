<?php
namespace AOE\AoeIpauth\Tests\Unit\Service;

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

use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IpMatchingServiceTest
 *
 * @package AOE\AoeIpauth\Tests\Unit\Service
 */
class IpMatchingServiceTest extends UnitTestCase
{

    /**
     * @var \AOE\AoeIpauth\Service\IpMatchingService
     */
    protected $fixture;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->fixture = GeneralUtility::makeInstance('AOE\\AoeIpauth\\Service\\IpMatchingService');
    }

    /**
     * tearDown
     */
    public function tearDown()
    {
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
    public static function isValidIpDeterminesIpValidityCorrectlyProvider()
    {
        return array(
            'valid simple ip' => array(
                '192.168.1.200', true
            ),
            'invalid simple ip (third touple exceeds 255)' => array(
                '234.119.260.65', false
            ),
            'invalid simple ip (not enough touples)' => array(
                '234.119.1', false
            ),
            'invalid simple ip (not enough touples, but enough dots)' => array(
                '234.119.1.', false
            ),
            'invalid simple ip (string at end)' => array(
                '234.119.1.x', false
            ),
            'invalid simple ip (string in middle)' => array(
                '234.119.x.1', false
            ),
            'invalid simple ip (negative number)' => array(
                '-1.119.1.1', false
            ),
            'invalid simple ip (cidr)' => array(
                '234.119.2.1/20', false
            ),
            'invalid simple ip (wildcard)' => array(
                '234.119.2.*', false
            ),
            'invalid simple ip (dash range)' => array(
                '234.119.2.1-234.119.2.10', false
            ),
        );
    }

    /**
     * @test
     * @dataProvider isValidIpDeterminesIpValidityCorrectlyProvider
     */
    public function isValidIpDeterminesIpValidityCorrectly($ip, $expected)
    {
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
    public static function isValidWildcardIpDeterminesIpValidityCorrectlyProvider()
    {
        return array(
            'valid simple wildcard' => array(
                '192.168.1.*', true
            ),
            'valid simple wildcard (wildcard in middle)' => array(
                '192.*.1.1', true
            ),
            'valid simple wildcard (wildcard in front)' => array(
                '*.1.1.1', true
            ),
            'valid simple wildcard (multiple wildcards)' => array(
                '1.*.*.*', true
            ),
            'valid simple wildcard (all wildcards)' => array(
                '*.*.*.*', true
            ),
            'invalid simple wildcard (too many wildcards)' => array(
                '*.*.*.*.*', false
            ),
            'invalid simple wildcard (cidr)' => array(
                '234.119.2.1/20', false
            ),
            'invalid simple wildcard (normal ip)' => array(
                '234.119.2.1', false
            ),
            'invalid simple wildcard (dash range)' => array(
                '234.119.2.1-234.119.2.10', false
            ),
        );
    }

    /**
     * @test
     * @dataProvider isValidWildcardIpDeterminesIpValidityCorrectlyProvider
     */
    public function isValidWildcardIpDeterminesIpValidityCorrectly($ip, $expected)
    {
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
    public static function isValidDashRangeDeterminesIpValidityCorrectlyProvider()
    {
        return array(
            'valid simple dash range' => array(
                '234.119.2.1-234.119.2.10', true
            ),
            'invalid simple dash range (left ip is invalid)' => array(
                '234.119.260.1-234.119.2.10', false
            ),
            'invalid simple dash range (right ip is invalid)' => array(
                '234.119.2.1-234.119.260.1', false
            ),
            'invalid simple ip (cidr)' => array(
                '234.119.2.1/20', false
            ),
            'invalid simple ip (wildcard)' => array(
                '234.119.2.*', false
            ),
            'invalid simple ip (' => array(
                '234.119.2.1', false
            ),
        );
    }

    /**
     * @test
     * @dataProvider isValidDashRangeDeterminesIpValidityCorrectlyProvider
     */
    public function isValidDashRangeDeterminesIpValidityCorrectly($ip, $expected)
    {
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
    public static function isValidCidrRangeDeterminesIpValidityCorrectlyProvider()
    {
        return array(
            'valid simple cidr range' => array(
                '234.119.2.1/20', true
            ),
            'invalid simple cidr range (suffix illegal)' => array(
                '234.119.2.1/0.5', false
            ),
            'invalid simple cidr range (suffix illegal #2)' => array(
                '234.119.2.1/-1', false
            ),
            'invalid simple cidr range (suffix too big)' => array(
                '234.119.2.1/50', false
            ),
            'invalid simple cidr range (ip wrong)' => array(
                '234.119.270.1/16', false
            ),
            'invalid simple ip (simple ip)' => array(
                '234.119.2.1', false
            ),
            'invalid simple ip (wildcard)' => array(
                '234.119.2.*', false
            ),
            'invalid simple ip (dash range)' => array(
                '234.119.2.1-234.119.2.10', false
            ),
        );
    }

    /**
     * @test
     * @dataProvider isValidCidrRangeDeterminesIpValidityCorrectlyProvider
     */
    public function isValidCidrRangeDeterminesIpValidityCorrectly($ip, $expected)
    {
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
    public static function isIpAllowedDeterminesIpValidityCorrectlyProvider()
    {
        return array(
            'whitelisted simple ip' => array(
                '234.119.2.1', '234.119.2.1', true
            ),
            'non-whitelisted simple ip' => array(
                '234.119.2.1', '234.119.2.2', false
            ),
            'whitelisted wildcard ip' => array(
                '234.119.2.1', '234.119.2.*', true
            ),
            'non-whitelisted wildcard ip' => array(
                '234.119.3.1', '234.119.2.*', false
            ),
            'whitelisted dash range' => array(
                '234.119.2.3', '234.119.2.1-234.119.2.10', true
            ),
            'whitelisted dash range (lower corner case)' => array(
                '234.119.2.1', '234.119.2.1-234.119.2.10', true
            ),
            'whitelisted dash range (upper corner case)' => array(
                '234.119.2.10', '234.119.2.1-234.119.2.10', true
            ),
            'non-whitelisted dash range' => array(
                '234.119.3.3', '234.119.2.4-234.119.2.9', false
            ),
            'non-whitelisted dash range (lower corner case)' => array(
                '234.119.2.1', '234.119.2.2-234.119.2.10', false
            ),
            'non-whitelisted dash range (upper corner case)' => array(
                '234.119.2.10', '234.119.2.1-234.119.2.9', false
            ),
            'whitelisted cidr range (low end)' => array(
                '234.119.2.1', '234.119.2.0/24', true
            ),
            'whitelisted cidr range (high end)' => array(
                '234.119.2.254', '234.119.2.0/24', true
            ),
            'non-whitelisted cidr range (first touple)' => array(
                '233.119.2.3', '235.119.2.0/24', false
            ),
            'non-whitelisted cidr range (second touple)' => array(
                '235.120.2.3', '235.119.2.0/24', false
            ),
            'non-whitelisted cidr range (third touple)' => array(
                '235.119.3.3', '235.119.2.0/24', false
            ),
        );
    }

    /**
     * @test
     * @dataProvider isIpAllowedDeterminesIpValidityCorrectlyProvider
     */
    public function isIpAllowedDeterminesIpValidityCorrectly($givenIp, $whitelist, $expected)
    {
        $this->assertSame($this->fixture->isIpAllowed($givenIp, $whitelist), $expected);
    }
}
