<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'IP Authentication',
    'description' => 'Authenticates users based on IP address settings',
    'category' => 'services',
    'author' => 'Tomas Norre Mikkelsen',
    'author_email' => 'dev@aoe.com',
    'author_company' => 'AOE GmbH',
    'state' => 'stable',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-11.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
