<?php

declare(strict_types=1);

namespace AOE\AoeIpauth\EventListener;

use AOE\AoeIpauth\Domain\Service\FeEntityService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\ModifyResolvedFrontendGroupsEvent;

class ModifyFeGroups
{
    public function __invoke(ModifyResolvedFrontendGroupsEvent $event): void
    {
        $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');
        $feEntityService = GeneralUtility::makeInstance(FeEntityService::class);
        $groups = $feEntityService->findAllGroupsAuthenticatedByIp($ip);
        if (!empty($groups)) {
            $newGroups = array_merge($event->getGroups(), $groups);
            $event->setGroups($newGroups);
        }
    }
}