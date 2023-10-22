<?php

namespace App\Components\UserRegistrationForm;

use App\Model\Entities\Category;
use App\Model\Entities\User;
use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

/**
 * Class UserRegistrationForm
 * @package App\Components\UserRegistrationForm
 *
 * @method onFinished()
 * @method onCancel()
 */
class UserRegistrationForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];

  private UsersFacade $usersFacade;
  private Nette\Security\Passwords $passwords;

  /**
   * UserRegistrationForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param UsersFacade $usersFacade
   */
  public function __construct(UsersFacade $usersFacade, Nette\Security\Passwords $passwords, Nette\ComponentModel\IContainer $parent = null, string $name = null){
    parent::__construct($parent, $name);
    $this->usersFacade=$usersFacade;
    $this->passwords=$passwords;
    $this->createSubcomponents();
  }

  private function createSubcomponents():void {
    $this->addText('name','Jméno a příjmení:')
      ->setRequired('Zadejte své jméno')
      ->setHtmlAttribute('maxlength',40)
      ->addRule(Form::MAX_LENGTH,'Jméno je příliš dlouhé, může mít maximálně 40 znaků.',40);
    $this->addEmail('email','E-mail')
      ->setRequired('Zadejte platný email')
      ->addRule(function(Nette\Forms\Controls\TextInput $input){
        try{
          $this->usersFacade->getUserByEmail($input->value);
        }catch (\Exception $e){
          //pokud nebyl uživatel nalezen (tj. je vyhozena výjimka), je to z hlediska registrace v pořádku
          return true;
        }
        return false;
      },'Uživatel s tímto e-mailem je již registrován.');
    $password=$this->addPassword('password','Heslo');
    $password
      ->setRequired('Zadejte požadované heslo')
      ->addRule(Form::MIN_LENGTH,'Heslo musí obsahovat minimálně 5 znaků.',5);
    $this->addPassword('password2','Heslo znovu:')
      ->addRule(Form::EQUAL,'Hesla se neshodují',$password);

    $this->addSubmit('ok','registrovat se')
      ->setHtmlAttribute('class','btn btn-primary')
      ->onClick[]=function(SubmitButton $button){

        //uložení uživatele
        $values=$this->getValues('array');
        $user = new User();
        $user->name=$values['name'];
        $user->email=$values['email'];
        $user->password=$this->passwords->hash($values['password']); //heslo samozřejmě rovnou hashujeme :)
        $this->usersFacade->saveUser($user);

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