<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Note
 * @package App\Model\Entities
 * @property int $noteId
 * @property Category m:hasOne
 * @property string $title
 * @property string $text
 * @property \DateTime|null $updated
 */
class Note extends Entity{

}