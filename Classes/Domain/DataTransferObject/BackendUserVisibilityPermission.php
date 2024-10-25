<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\BackendWorkspaceRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DTO: Permission access Backend User Groups
 */
final class BackendUserVisibilityPermission extends AbstractPermission
{
    use PermissionTrait;

    /**
     * @var string
     */
    public const KEY = 'my_user_management_visibility_permissions';

    /**
     * @return void
     */
    protected function populateData(): void
    {
        $this->data = [
            'header' => 'LLL:EXT:my_user_management/Resources/Private/Language/locallang_be.xlf:backend_access_visibility_permissions',
            'items' => [],
        ];
        foreach ($this->getBackendGroupsForList() as $group) {
            $this->data['items'][$group['uid']] = [
                $group['title'],
                'EXT:my_user_management/Resources/Public/Icons/table-user-group-backend.svg',
                $group['description'],
            ];
        }
    }

    protected function getBackendGroupsForList(): array
    {
        $queryBuilder = $this->getQueryBuilderForTable('be_groups');
        $query = $queryBuilder->select('uid', 'title', 'description')
            ->from('be_groups');

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Get QueryBuilder without any default restrictions
     *
     * @param  string  $table
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected function getQueryBuilderForTable(string $table): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        // Show all records except versioning placeholders
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(BackendWorkspaceRestriction::class))
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder;
    }

    /**
     * Get configured options based on current backend user
     *
     * @todo remove if not used
     * @return array
     */
    public static function configured()
    {
        $configured = [];
        $backendUser = $GLOBALS['BE_USER'];
        // Only return allowed users for non-admin
        if ($backendUser instanceof BackendUserAuthentication && $backendUser->isAdmin() === false) {
            $options = $backendUser->groupData['custom_options'];
            foreach (GeneralUtility::trimExplode(',', $options, true) as $value) {
                if (strpos($value, static::KEY) === 0) {
                    $configured[] = (int)substr($value, strlen(static::KEY) + 1);
                }
            }
        }
        return $configured;
    }


    /**
     * @param  int  $group
     * @return bool
     */
    public static function hasAccessToGroup(int $group): bool
    {
        return static::isConfigured($group);
    }
}
