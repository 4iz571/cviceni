<?php

namespace App\Components\UserLoginForm;

use App\Model\Entities\Category;
use App\Model\Entities\User;
use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

/**
 * Class UserLoginForm
 * @package App\Components\UserLoginForm
 *
 * @method onFinished()
 * @method onCancel()
 */
class UserLoginForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public array $onFinished = [];
  /** @var callable[] $onCancel */
  public array $onCancel = [];

  /**
   * UserRegistrationForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param UsersFacade $usersFacade
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null){
    parent::__construct($parent, $name);
    $this->createSubcomponents();
  }

  private function createSubcomponents():void {
    $this->addEmail('email','E-mail')
      ->setRequired('Zadejte platný email');
    $password=$this->addPassword('password','Heslo')
      ->setRequired('Zadejte své heslo');

    $this->addSubmit('ok','přihlásit se')
      ->setHtmlAttribute('class','btn btn-primary')
      ->onClick[]=function(SubmitButton $button){
        $this->onFinished();
      };
    $this->addSubmit('storno','zrušit')
      ->setValidationScope([])
      ->setHtmlAttribute('class','btn btn-light')
      ->onClick[]=function(SubmitButton $button){
        $this->onCancel();
      };
  }

}