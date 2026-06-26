<?php

namespace KoninklijkeCollective\MyUserManagement\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use KoninklijkeCollective\MyUserManagement\Service\OnlineSessionService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Model for backend user
 */
final class BackendUser extends \TYPO3\CMS\Beuser\Domain\Model\BackendUser
{
    public const TABLE = 'be_users';

    protected ?array $_dbMountPoints = null;

    protected ?array $inheritedMountPoints = null;

    protected ?array $activeMountPoints = null;

    protected ?BackendUser $createdBy = null;

    protected int $disable = 0;

    protected int $admin = 0;

    protected string $userName = '';

    protected string $realName = '';


    /**
     * Override default backend user groups to map own custom model
     *
     * @var ObjectStorage<\KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup>
     */

    protected ObjectStorage $backendUserGroups;

    public function getBackendUserGroups(): ObjectStorage
    {
        return $this->backendUserGroups;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getRealName(): string
    {
        return $this->realName;
    }

    public function getIsAdmin() {
        return $this->admin;
    }

    public function getIsDisabled(): bool
    {
        return $this->disable;
    }

    /**
     * @return int[]
     */
    public function getDatabaseMountPoints(): array
    {
        if ($this->_dbMountPoints === null) {
            $this->_dbMountPoints = GeneralUtility::intExplode(',', $this->dbMountPoints, true);
        }

        return $this->_dbMountPoints;
    }

    /**
     * Returns all generated MountPoints
     *
     * @return array
     */
    public function getInheritedMountPoints(): array
    {
        if ($this->inheritedMountPoints === null) {
            $mounts = $this->getDatabaseMountPoints();

            foreach ($this->getBackendUserGroups() as $group) {
                /** @var \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup $group */
                $mounts = $this->getAllDatabaseMountsFromUserGroup($group, $mounts);
            }
            $this->setInheritedMountPoints($mounts);
        }

        return $this->inheritedMountPoints;
    }

    /**
     * @param  \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup  $group
     * @param  array  $mounts
     * @return array
     */
    protected function getAllDatabaseMountsFromUserGroup(BackendUserGroup $group, array $mounts = []): array
    {
        $dbMounts = $group->getDatabaseMountPoints();
        $mounts = array_unique(array_merge($mounts, $dbMounts));

        if ($group->getSubGroups() !== null) {
            foreach ($group->getSubGroups() as $subGroup) {
                /** @var \KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup $subGroup */
                $mounts = $this->getAllDatabaseMountsFromUserGroup($subGroup, $mounts);
            }
        }

        return $mounts;
    }

    public function setInheritedMountPoints(array $allInheritedMountPoints): BackendUser
    {
        $this->inheritedMountPoints = $allInheritedMountPoints;

        return $this;
    }

    public function getActiveMountPoints(): ?array
    {
        return $this->activeMountPoints;
    }

    public function setActiveMountPoints(array $activePageMount): BackendUser
    {
        $this->activeMountPoints = $activePageMount;

        return $this;
    }

    public function getCreatedBy(): ?BackendUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(BackendUser $createdBy): BackendUser
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Override function to actually look into session table
     */
    public function isCurrentlyLoggedIn(): bool
    {
        return GeneralUtility::makeInstance(OnlineSessionService::class)
            ->userIsCurrentlyLoggedIn($this);
    }


}
