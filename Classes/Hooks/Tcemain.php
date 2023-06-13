<?php
namespace AOE\AoeIpauth\Hooks;

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
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use AOE\AoeIpauth\Service\IpMatchingService;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class Tcemain
 *
 * @package AOE\AoeIpauth\Hooks
 */
class Tcemain
{

    const IP_TABLE = 'tx_aoeipauth_domain_model_ip';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Post process
     *
     * @param string $status
     * @param string $table
     * @param string $id
     * @param array $fieldArray
     * @param DataHandler $pObj
     * @return void
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$pObj)
    {
        /** @var ObjectManager $this->objectManager */
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        if (self::IP_TABLE != $table || empty($fieldArray) || !isset($fieldArray['ip'])) {
            return;
        }

        /** @var IpMatchingService $ipMatchingService */
        $ipMatchingService = $this->objectManager->get(IpMatchingService::class);

        $potentialIp = $fieldArray['ip'];

        // If it is a valid IP, return. No further action needed.
        $isValidIp = $ipMatchingService->isValidIp($potentialIp);
        if ($isValidIp) {
            $fieldArray['range_type'] = IpMatchingService::NORMAL_IP_TYPE;
            return;
        }

        // Allow wildcard notations
        $isValidWildcard = $ipMatchingService->isValidWildcardIp($potentialIp);
        if ($isValidWildcard) {
            $fieldArray['range_type'] = IpMatchingService::WILDCARD_IP_TYPE;
            return;
        }

        // Allow dash-range notations
        $isValidDashRange = $ipMatchingService->isValidDashRange($potentialIp);
        if ($isValidDashRange) {
            $fieldArray['range_type'] = IpMatchingService::DASHRANGE_IP_TYPE;
            return;
        }

        // Check if it is a valid CIDR range
        $isValidRange = $ipMatchingService->isValidCidrRange($potentialIp);
        if ($isValidRange) {
            $fieldArray['range_type'] = IpMatchingService::CIDR_IP_TYPE;
            return;
        }

        // Neither a valid IP nor a valid range
        unset($fieldArray['ip']);

        $this->addFlashMessage(
            'The new IP (<strong>' . $potentialIp . '</strong>) ' .
                'you entered was neither a valid IP nor a valid range. ' .
                'The change was rejected.',
            AbstractMessage::ERROR
        );
    }

    /**
     * Adds a simple flash message
     *
     * @param string $message
     * @param int $code
     * @return void
     */
    protected function addFlashMessage($message, $code)
    {
        $flashMessage = GeneralUtility::makeInstance(FlashMessage::class,
            $message,
            '',
            $code,
            true
        );

        $flashMessageService = $this->objectManager->get(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($flashMessage);
    }
}
