<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Todo
 * @package App\Model\Entities
 * @property int $todoId
 * @property string $title
 * @property string $description = ''
 * @property \DateTimeImmutable|null $deadline = null
 * @property bool $completed = false
 * @property Tag[] $tags m:hasMany
 * @property TodoItem[] $todoItems m:belongsToMany
 *
 * @method addToTags(Tag $tag)
 * @method removeFromTags(Tag $tag)
 * @method removeAllTags()
 *
 * @method addToTodoItems(TodoItem $todoItem)
 * @method removeFromTodoItems(TodoItem $todoItem)
 * @method removeAllTodoItems()
 */
class Todo extends Entity{

}