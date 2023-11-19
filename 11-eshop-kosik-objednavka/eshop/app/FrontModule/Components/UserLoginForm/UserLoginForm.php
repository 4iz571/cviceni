<?php

namespace App\FrontModule\Components\UserLoginForm;

use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class UserLoginForm
 * @package App\FrontModule\Components\UserLoginForm
 *
 * @method onFinished()
 * @method onCancel()
 */
class UserLoginForm extends Form{

  use SmartObject;

  public array $onFinished = [];
  public array $onCancel = [];

  /**
   * UserRegistrationForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null){
    parent::__construct($parent, $name);
    $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
    $this->createSubcomponents();
  }

  private function createSubcomponents():void {
    $this->addEmail('email','E-mail')
      ->setRequired('Zadejte platný email');
    $password=$this->addPassword('password','Heslo')
      ->setRequired('Zadejte své heslo');

    $this->addSubmit('ok','přihlásit se')
      ->onClick[]=function(SubmitButton $button){
        $this->onFinished();
      };
    $this->addSubmit('storno','zrušit')
      ->setValidationScope([])
      ->onClick[]=function(SubmitButton $button){
        $this->onCancel();
      };
  }

}