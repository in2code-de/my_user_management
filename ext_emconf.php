<?php

$EM_CONF['my_user_management'] = [
    'title' => 'My Backend User Management',
    'description' => 'A module that makes it possible for editors to maintain backend users.',
    'category' => 'module',
    'version' => '9.0.0',
    'state' => 'stable',
    'uploadFolder' => false,
    'clearCacheOnLoad' => true,
    'author' => 'Benjamin Serfhos',
    'author_email' => 'benjamin@serfhos.com',
    'author_company' => 'Rotterdam School of Management, Erasmus University',
    'constraints' => [
        'depends' => [
            'typo3' => '^13.0.0',
            'beuser' => '^13.0.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'KoninklijkeCollective\\MyUserManagement\\' => 'Classes',
        ],
    ],
];
