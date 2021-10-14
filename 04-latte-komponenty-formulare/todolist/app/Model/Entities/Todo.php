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
 * @method addToTodos(Todo $todo)
 * @method removeFromTodos(Todo $todo)
 * @method removeAllTodos()
 */
class Todo extends Entity{

}