<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers\Uri;

use InvalidArgumentException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Remove Record ViewHelper, see FormEngine logic
 *
 * @internal
 */
final class RemoveRecordViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('uid', 'int', 'uid of record to be deleted', true);
        $this->registerArgument('table', 'string', 'target database table', true);
        $this->registerArgument('returnUrl', 'string', '', false, '');
    }

    public function render(): string
    {
        if ($this->arguments['uid'] < 1) {
            throw new InvalidArgumentException(
                'Uid must be a positive integer, ' . $this->arguments['uid'] . ' given.',
                1574000004
            );
        }

        $returnUrl = $this->arguments['returnUrl'] ?: GeneralUtility::getIndpEnv('REQUEST_URI');

        $parameters = [
            'cmd' => [$this->arguments['table'] => [$this->arguments['uid'] => ['delete' => 1]]],
            'redirect' => $returnUrl,
        ];

        return (string)GeneralUtility::makeInstance(UriBuilder::class)->buildUriFromRoute('tce_db', $parameters);
    }
}
