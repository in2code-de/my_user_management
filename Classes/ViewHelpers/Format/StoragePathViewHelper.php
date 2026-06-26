<?php
namespace KoninklijkeCollective\MyUserManagement\ViewHelpers\Format;

use KoninklijkeCollective\MyUserManagement\Service\StorageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper: Format Storage Location
 */
final class StoragePathViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('storageId', 'int', '');
        $this->registerArgument('location', 'string', '', false, '/');
    }

    public function render(): string
    {
        return GeneralUtility::makeInstance(StorageService::class)->path(
            $this->arguments['storageId'],
            $this->arguments['location'] ?? '/'
        );
    }
}
