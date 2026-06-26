<?php

use KoninklijkeCollective\MyUserManagement\Hook\ButtonBarHook;
use KoninklijkeCollective\MyUserManagement\Hook\DataHandlerCheckModifyAccessListHook;
use TYPO3\CMS\Beuser\Domain\Model\BackendUser;

defined('TYPO3') or die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['checkModifyAccessList']['my_user_management'] =
    DataHandlerCheckModifyAccessListHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook']['my_user_management'] =
    ButtonBarHook::class . '->getButtons';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][BackendUser::class] = [
    'className' => \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser::class,
];
