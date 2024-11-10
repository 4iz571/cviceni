<?php

namespace App\Components\UserSecretCodeForm;

use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextInput;
use Nette\SmartObject;
use PragmaRX\Google2FA\Google2FA;

/**
 * Class UserSecretCodeForm
 * @package App\Components\UserSecretCodeForm
 *
 * @method onFinished(string $message)
 */
class UserSecretCodeForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];

  private UsersFacade $usersFacade;
  private Nette\Security\User $user;

  /**
   * UserRegistrationForm constructor.
   * @param UsersFacade $usersFacade
   * @param Nette\Security\Passwords $passwords
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   */
  public function __construct(UsersFacade $usersFacade, Nette\Security\User $user, Nette\ComponentModel\IContainer $parent = null, string $name = null){
    parent::__construct($parent, $name);
    $this->user=$user;
    $this->usersFacade=$usersFacade;
  }

  public function createSubcomponents(bool $newCode):void{
    $secretCode=$this->addText('secret_code','Bezpečnostní kód z autentizátoru:');
    $secretCode->setRequired('Zadejte bezpečnostní kód z aplikace ve svém telefonu');

    $submitOk = $this->addSubmit('ok','odeslat kód');
    $submitOk->setHtmlAttribute('class','btn btn-primary');

    if ($newCode){
      #region form pro nastavení nového kódu pro uživatele
      $newSecretCode = $this->addHidden('new_secret_code',null);

      $secretCode->addRule(function(TextInput $input)use($newSecretCode){
        #region kontrola zadaného kódu
        $google2FA = new Google2FA();
        return $google2FA->verify($input->value, $newSecretCode->value, 10 /*počet tolerovaných změn kódu podle timestampu - á 30s*/);
        #region kontrola zadaného kódu
      },'Chybný bezpečnostní kód, zkuste to znovu');


      $submitOk->onClick[]=function(SubmitButton $button){
        $values = $button->form->getValues('array');
        $currentUser = $this->usersFacade->getUser($this->user->id);
        $currentUser->secretCode=$values['new_secret_code'];
        $this->usersFacade->saveUser($currentUser);

        $this->onFinished('2FA byla nastavena.');
      };

      #endregion form pro nastavení nového kódu pro uživatele
    }else{
      #region form pro ověření kódu
      $secretCode->addRule(function(TextInput $input){
        #region kontrola zadaného kódu
        $google2FA = new Google2FA();
        $currentUser=$this->usersFacade->getUser($this->user->id);
        return $google2FA->verify($input->value, $currentUser->secretCode, 10 /*počet tolerovaných změn kódu podle timestampu - á 30s*/);
        #region kontrola zadaného kódu
      },'Chybný bezpečnostní kód, zkuste to znovu');


      $submitOk->onClick[]=function(SubmitButton $button){
        //aktualizujeme identitu aktuálního uživatele - bez nastavování role require2fa
        $currentUser=$this->usersFacade->getUser($this->user->id);
        $this->user->login($this->usersFacade->getUserIdentity($currentUser));

        $this->onFinished('');
      };
      #endregion form pro ověření kódu
    }
  }

}