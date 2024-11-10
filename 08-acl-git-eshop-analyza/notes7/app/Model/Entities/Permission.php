<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Permission
 * @package App\Model\Entities
 * @author Stanislav Vojíř
 * @property int $permissionId
 * @property string $roleId
 * @property string $resourceId
 * @property string $action
 * @property string $type = 'allow' m:Enum(self::TYPE_*)
 * @property-read Role $role m:hasOne
 * @property-read Resource $resource m:hasOne
 */
class Permission extends Entity{

  const TYPE_ALLOW = 'allow';
  const TYPE_DENY = 'deny';

}