<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Category
 * @package App\Model\Entities
 * @property int $categoryId
 * @property string $title
 * @property string $description
 * @property-read Note[] $notes m:belongsToMany
 */
class Category extends Entity{

}