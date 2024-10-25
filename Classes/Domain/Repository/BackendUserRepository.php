<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Repository;

use DateTime;
use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;
use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObjects\BackendUserVisibilityPermission;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use KoninklijkeCollective\MyUserManagement\Functions\BackendUserAuthenticationTrait;
use TYPO3\CMS\Beuser\Domain\Model\Demand;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

final class BackendUserRepository extends \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository
{
    use BackendUserAuthenticationTrait;

    protected $defaultOrderings = ['username' => QueryInterface::ORDER_ASCENDING];

    public function findByUid($uid): ?BackendUser
    {
        $query = $this->createQuery();

        return $query->matching($query->equals('uid', $uid))->execute()->getFirst();
    }

    /**
     * Override demanded query for filtering by group access
     */
    public function findDemanded(Demand $demand)
    {
        $result = parent::findDemanded($demand);

        // Do query again with configured permissions applied
        if (!$this->getBackendUserAuthentication()->isAdmin()) {
            $query = $result->getQuery();
            $this->applyUserGroupPermission($query);
            $this->applyGroupConstraint($query);
            return $query->execute();
        }

        return $result;
    }

    public function findAllActive(): array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->logicalAnd(
            $query->equals('deleted', false),
            $query->equals('disable', false),
        ));

        $this->applyUserGroupPermission($query);

        return $query->execute();
    }

    public function findAllInactive(DateTime $lastLoginSince): array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->logicalAnd(
            $query->equals('deleted', false),
            $query->lessThanOrEqual('lastlogin', $lastLoginSince),
        ));

        $query = $this->applyUserGroupPermission($query);

        return $query->execute();
    }

    public function applyUserGroupPermission(QueryInterface $query): QueryInterface
    {
        if (!$this->getBackendUserAuthentication()->isAdmin()) {
            $constraints = [
                $query->getConstraint(),
                $query->logicalNot($query->like('username', '_cli_%')),
                $query->logicalNot($query->equals('uid', $this->getBackendUserAuthentication()->user['uid']))

            ];

            $query->matching($query->logicalAnd(...$constraints));
        }

        return $query;
    }

    private function applyGroupConstraint(QueryInterface $query)
    {
        $groupConstraint = array();
        $groups = BackendUserVisibilityPermission::getConfigured();
        foreach($groups as $element)
        {
            $groupConstraint[] = $query->logicalOr([
                $query->equals('usergroup', (int)$element),
                $query->like('usergroup', (int)$element . ',%'),
                $query->like('usergroup', '%,' . (int)$element),
                $query->like('usergroup', '%,' . (int)$element . ',%'),
            ]);
        }
        $groupConstraint[] =
            $query->logicalOr([
                $query->equals('usergroup', ''),
                $query->equals('usergroup', null),
                $query->equals('usergroup', 0),
            ]);
        $query->matching(
            $query->logicalAnd(
                $query->equals('deleted', 0),
                $query->logicalOr($groupConstraint)
            )
        );
    }

}
