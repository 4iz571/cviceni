<?php

namespace App\Components\UserLoginForm;

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
  public $onFinished = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];

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

  private function createSubcomponents(){
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