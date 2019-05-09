<?php
namespace AOE\AoeIpauth\Service;

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
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Class IpMatchingService
 *
 * @package AOE\AoeIpauth\Service
 */
class IpMatchingService implements \TYPO3\CMS\Core\SingletonInterface
{

    const NORMAL_IP_TYPE = 0;
    const WILDCARD_IP_TYPE = 2;
    const CIDR_IP_TYPE = 1;
    const DASHRANGE_IP_TYPE = 3;

    /**
     * Checks if an IP is valid
     * Allows IPv4 and IPv6
     *
     * @param string $possibleIp
     * @return bool
     */
    public function isValidIp($possibleIp)
    {
        $isValid = GeneralUtility::validIP($possibleIp);
        return $isValid;
    }

    /**
     * Checks if an IP is valid (containing wildcards)
     * Only works for IPv4
     *
     * @param string $possibleIp
     * @return bool
     */
    public function isValidWildcardIp($possibleIp)
    {
        $numberOfWildcards = substr_count($possibleIp, '*');
        // Minimum 1, Maximum 4 wildcards
        if (0 >= $numberOfWildcards || 4 < $numberOfWildcards) {
            return false;
        }
        // Replace wildcards and simply validate the IP
        $normalizedPossibleIp = str_replace('*', '50', $possibleIp);

        $isValid = GeneralUtility::validIPv4($normalizedPossibleIp);
        return $isValid;
    }

    /**
     * Checks if an IP range is valid (containing dash notation)
     * E.g. 192.168.1.0-192.168.1.200
     *
     * @param string $possibleRange
     * @return bool
     */
    public function isValidDashRange($possibleRange)
    {
        $ips = explode('-', $possibleRange);
        if (2 !== count($ips)) {
            return false;
        }
        $lower = $ips[0];
        $upper = $ips[1];
        $isValid = ($this->isValidIp($lower) && $this->isValidIp($upper));
        return $isValid;
    }

    /**
     * Checks if a string is a
     *
     * @param string $possibleRange
     * @return bool
     */
    public function isValidCidrRange($possibleRange)
    {
        $doesMatch = preg_match(
            '#^((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.){3}(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)/(3[0-2]|[1-2]?[0-9])$#i',
            $possibleRange
        );
        return (1 === $doesMatch);
    }

    /**
     * Checks if an IP is allowed when matched against a whitelist
     *
     * @see http://pgregg.com/blog/2009/04/php-algorithms-determining-if-an-ip-is-within-a-specific-range/
     * @see http://pgregg.com/projects/php/ip_in_range/ip_in_range.phps
     *
     * @param string $givenIp The IP to check
     * @param string $givenWhitelist The ip/range the given ip needs to match;
     * @return bool
     */
    public function isIpAllowed($givenIp, $givenWhitelist)
    {
        $isAllowed = false;

        if ($this->isValidIp($givenWhitelist)) {
            // Simple IP
            $isAllowed = ($givenIp == $givenWhitelist);
        } elseif ($this->isValidCidrRange($givenWhitelist)) {
            // CIDR
            list($range, $netmask) = explode('/', $givenWhitelist, 2);
            $x = explode('.', $range);
            while (count($x) < 4) {
                $x[] = '0';
            }
            list($a, $b, $c, $d) = $x;
            $range = sprintf(
                '%u.%u.%u.%u',
                empty($a) ? '0' : $a,
                empty($b) ?' 0' : $b,
                empty($c) ? '0' : $c,
                empty($d) ? '0' : $d
            );
            $rangeDec = ip2long($range);
            $ipDec = ip2long($givenIp);

            // Create netmask
            $wildcardDec = pow(2, (32 - $netmask)) - 1;
            $netmaskDec = ~ $wildcardDec;

            $isAllowed = (($ipDec & $netmaskDec) == ($rangeDec & $netmaskDec));
        } else {
            // Wildcard or dash range. Both get matched very similarly.
            $dashRange = $givenWhitelist;

            // Transform wildcard to dash range
            if (false !== strpos($givenWhitelist, '*')) {
                // a.b.*.* format, converts to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $givenWhitelist);
                $upper = str_replace('*', '255', $givenWhitelist);
                $dashRange = $lower . '-' . $upper;
            }

            // Validate dash range
            if (false !== strpos($dashRange, '-')) {
                list($lower, $upper) = explode('-', $dashRange, 2);
                $lowerDec = (float)sprintf('%u', ip2long($lower));
                $upperDec = (float)sprintf('%u', ip2long($upper));
                $givenIpDec = (float)sprintf('%u', ip2long($givenIp));
                $isAllowed =  (($givenIpDec >= $lowerDec) && ($givenIpDec <= $upperDec));
            }
        }
        return $isAllowed;
    }
}
