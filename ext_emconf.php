<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'IP Authentication',
    'description' => 'Authenticates users based on IP address settings',
    'category' => 'services',
    'author' => 'Tomas Norre Mikkelsen',
    'author_email' => 'dev@aoe.com',
    'author_company' => 'AOE GmbH',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
