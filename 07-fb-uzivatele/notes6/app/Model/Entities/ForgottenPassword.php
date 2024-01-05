<?php

namespace App\Model\Entities;

use Dibi\DateTime;
use LeanMapper\Entity;

/**
 * Class ForgottenPassword
 * @package App\Model\Entities
 * @property string $forgottenPasswordId
 * @property User $user m:hasOne
 * @property string $code
 * @property-read DateTime $created
 */
class ForgottenPassword extends Entity{

}