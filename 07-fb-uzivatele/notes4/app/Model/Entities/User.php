<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class User
 * @package App\Model\Entities
 * @property int $userId
 * @property string $name
 * @property Role $role m:hasOne
 * @property string $email
 * @property string $password
 */
class User extends Entity{

}