<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class TodoItem
 * @package App\Model\Entities
 * @property int $todoItemId
 * @property Todo $todo m:hasOne
 * @property string $title
 * @property bool $completed = false
 */
class TodoItem extends Entity{

}