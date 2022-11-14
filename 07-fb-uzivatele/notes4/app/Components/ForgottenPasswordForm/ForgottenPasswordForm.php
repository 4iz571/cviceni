<?php

namespace App\Components\ForgottenPasswordForm;

use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

/**
 * Class ForgottenPasswordForm
 * @package App\Components\ForgottenPasswordForm
 *
 * @method onFinished($message='')
 * @method onCancel()
 */
class ForgottenPasswordForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];

  private UsersFacade $usersFacade;

  /**
   * ForgottenPasswordForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param UsersFacade $usersFacade
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, UsersFacade $usersFacade){
    parent::__construct($parent, $name);
    $this->usersFacade=$usersFacade;
    $this->createSubcomponents();
  }

  private function createSubcomponents():void{
    $this->addEmail('email','E-mail')
      ->setRequired('Zadejte platný email');

    $this->addSubmit('ok','poslat e-mail pro obnovu hesla')
      ->setHtmlAttribute('class','btn btn-primary')
      ->onClick[]=function(SubmitButton $button){

        //TODO chceme uživateli poslat e-mail s odkazem pro změnu hesla
        //odkaz bude na akci User:renewPassword s parametry user a code

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