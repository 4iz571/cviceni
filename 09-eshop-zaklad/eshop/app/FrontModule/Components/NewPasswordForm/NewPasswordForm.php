<?php

namespace App\FrontModule\Components\NewPasswordForm;

use App\Model\Entities\User;
use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class NewPasswordForm
 * @package App\FrontModule\Components\NewPasswordForm
 *
 * @method onFinished($message='')
 * @method onFailed($message='')
 * @method onCancel()
 */
class NewPasswordForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onFailed */
  public $onFailed = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];

  /** @var UsersFacade $usersFacade */
  private $usersFacade;
  /** @var Nette\Security\Passwords $passwords */
  private $passwords;

  /**
   * ForgottenPasswordForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param UsersFacade $usersFacade
   * @param Nette\Security\Passwords $passwords
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, UsersFacade $usersFacade, Nette\Security\Passwords $passwords){
    parent::__construct($parent, $name);
    $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
    $this->usersFacade=$usersFacade;
    $this->passwords=$passwords;
    $this->createSubcomponents();
  }

  private function createSubcomponents(){
    $this->addHidden('userId');

    $password=$this->addPassword('password','Heslo');
    $password
      ->setRequired('Zadejte požadované heslo')
      ->addRule(Form::MIN_LENGTH,'Heslo musí obsahovat minimálně 5 znaků.',5);
    $this->addPassword('password2','Heslo znovu:')
      ->addRule(Form::EQUAL,'Hesla se neshodují',$password);

    $this->addSubmit('ok','uložit nové heslo')
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
  public function setDefaults($values, bool $erase = false){
    if ($values instanceof User){
      $this->setDefaults(['userId'=>$values->userId]);
    }
    parent::setDefaults($values, $erase);
    return $this;
  }

}