<?php

namespace App\Components\NewPasswordForm;

use App\Model\Entities\User;
use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

/**
 * Class NewPasswordForm
 * @package App\Components\NewPasswordForm
 *
 * @method onFinished($message='')
 * @method onFailed($message='')
 * @method onCancel()
 */
class NewPasswordForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public array $onFinished = [];
  /** @var callable[] $onFailed */
  public array $onFailed = [];
  /** @var callable[] $onCancel */
  public array $onCancel = [];

  private UsersFacade $usersFacade;
  private Nette\Security\Passwords $passwords;

  /**
   * ForgottenPasswordForm constructor.
   * @param UsersFacade $usersFacade
   * @param Nette\Security\Passwords $passwords
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   */
  public function __construct(UsersFacade $usersFacade, Nette\Security\Passwords $passwords,Nette\ComponentModel\IContainer $parent = null, string $name = null){
    parent::__construct($parent, $name);
    $this->usersFacade=$usersFacade;
    $this->passwords=$passwords;
    $this->createSubcomponents();
  }

  private function createSubcomponents():void {
    $this->addHidden('userId');

    $password=$this->addPassword('password','Heslo');
    $password
      ->setRequired('Zadejte požadované heslo')
      ->addRule(Form::MIN_LENGTH,'Heslo musí obsahovat minimálně 5 znaků.',5);
    $this->addPassword('password2','Heslo znovu:')
      ->addRule(Form::EQUAL,'Hesla se neshodují',$password);

    $this->addSubmit('ok','uložit nové heslo')
      ->setHtmlAttribute('class','btn btn-primary')
      ->onClick[]=function(SubmitButton $button){
        //uložení nového hesla
        $values=$this->getValues('array');

        try{
          $user = $this->usersFacade->getUser($values['userId']);
        }catch (\Exception $e){
          $this->onFailed('Zvolený uživatelský účet nebyl nalezen.');
          return;
        }

        $user->password=$this->passwords->hash($values['password']); //heslo samozřejmě rovnou hashujeme :)
        $this->usersFacade->saveUser($user);

        $this->onFinished('Heslo bylo změněno.');
      };
    $this->addSubmit('storno','zrušit')
      ->setValidationScope([])
      ->setHtmlAttribute('class','btn btn-light')
      ->onClick[]=function(SubmitButton $button){
        $this->onCancel();
      };
  }

  /**
   * Metoda pro nastavení výchozích hodnot
   * @param array|object|User $values
   * @param bool $erase = false
   * @return $this
   */
  public function setDefaults($values, bool $erase = false):self {
    if ($values instanceof User){
      $this->setDefaults(['userId'=>$values->userId]);
    }
    parent::setDefaults($values, $erase);
    return $this;
  }

}