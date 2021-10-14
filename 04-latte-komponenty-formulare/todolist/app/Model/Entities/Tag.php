<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Tag
 * @package App\Model\Entities
 * @property int $tagId
 * @property string $title
 * @property Todo[] $todos m:hasMany(:todo_tag::)
 */
class Tag extends Entity{

}