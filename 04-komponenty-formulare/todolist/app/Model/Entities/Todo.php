<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Todo
 * @package App\Model\Entities
 * @property int $todoId
 * @property string $title
 * @property string $description = ''
 * @property \DateTime|null $deadline = null
 * @property bool $completed = false
 * @property Tag[] $tags m:hasMany
 *
 * @method addToTags(Tag $tag)
 * @method removeFromTags(Tag $tag)
 * @method removeAllTags()
 */
class Todo extends Entity{

}