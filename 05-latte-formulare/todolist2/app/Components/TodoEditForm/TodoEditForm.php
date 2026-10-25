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
  public array $onFinished = [];
  /** @var callable[] $onFailed */
  public array $onFailed = [];
  /** @var callable[] $onCancel */
  public array $onCancel = [];

  //TODO tady asi něco chybí :)


  public function setDefaults(array|object $values, bool $erase = false):static {
    parent::setDefaults($values, $erase);
    return $this;
  }

}