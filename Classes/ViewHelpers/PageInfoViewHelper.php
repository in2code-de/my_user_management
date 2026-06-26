<?php

namespace KoninklijkeCollective\MyUserManagement\ViewHelpers;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Retrieve page information for given id
 */
final class PageInfoViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('pageId', 'integer', 'Page ID to retrieve information about', true);
        $this->registerArgument('as', 'string', 'Variable to use', false, 'page');
    }

    public function render(): string
    {
        $variableProvider = $this->renderingContext->getVariableProvider();
        $variableProvider->add($this->arguments['as'], GeneralUtility::makeInstance(PageRepository::class)->getPage($this->arguments['pageId']));
        $output = $this->renderChildren();
        $variableProvider->remove($this->arguments['as']);

        return $output;
    }
}
