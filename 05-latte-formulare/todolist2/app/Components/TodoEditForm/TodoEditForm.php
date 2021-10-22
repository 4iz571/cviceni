<?php

namespace App\Components\TodoEditForm;

use Nette\Application\UI\Form;
use Nette\SmartObject;

/**
 * Class TodoEditForm
 * @package App\Components\TodoEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class TodoEditForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onFailed */
  public $onFailed = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];

  //TODO tady asi něco chybí :)


  public function setDefaults($values, bool $erase = false):self {
    parent::setDefaults($values, $erase);
    return $this;
  }

}