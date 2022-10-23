<?php

namespace App\Presenters;

use App\Components\UserLoginForm\UserLoginForm;
use App\Components\UserLoginForm\UserLoginFormFactory;
use App\Components\UserRegistrationForm\UserRegistrationForm;
use App\Components\UserRegistrationForm\UserRegistrationFormFactory;
use App\Model\Facades\UsersFacade;
use Nette;

/**
 * Class UserPresenter - presenter pro akce týkající se uživatelů
 * @package App\Presenters
 */
class UserPresenter extends BasePresenter{
  private UsersFacade $usersFacade;
  private UserLoginFormFactory $userLoginFormFactory;
  private UserRegistrationFormFactory $userRegistrationFormFactory;

  /**
   * Akce pro odhlášení uživatele
   * @throws Nette\Application\AbortException
   */
  public function actionLogout():void {
    if ($this->user->isLoggedIn()){
      $this->user->logout();
    }
    $this->redirect('Homepage:default');
  }

  /**
   * Akce pro přihlášení - pokud už je uživatel přihlášen, tak ho jen přesměrujeme na homepage
   * @throws Nette\Application\AbortException
   */
  public function actionLogin():void {
    if ($this->user->isLoggedIn()){
      $this->redirect('Homepage:default');
    }
  }

  /**
   * Akce pro registraci - pokud už je uživatel přihlášen, tak ho jen přesměrujeme na homepage
   * @throws Nette\Application\AbortException
   */
  public function actionRegister():void {
    if ($this->user->isLoggedIn()){
      $this->redirect('Homepage:default');
    }
  }

  /**
   * Formulář pro přihlášení existujícího uživatele
   * @return UserLoginForm
   */
  protected function createComponentUserLoginForm():UserLoginForm {
    $form=$this->userLoginFormFactory->create();
    $form->onFinished[]=function()use($form){
      $values=$form->getValues('array');
      try{
        $this->user->login($values['email'],$values['password']);
      }catch (\Exception $e){
        $this->flashMessage('Neplatná kombinace e-mailu a hesla!','error');
        $this->redirect('login');
      }
      $this->redirect('Homepage:default');
    };
    $form->onCancel[]=function()use($form){
      $this->redirect('Homepage:default');
    };
    return $form;
  }

  /**
   * Formulář pro registraci nového uživatele
   * @return UserRegistrationForm
   */
  protected function createComponentUserRegistrationForm():UserRegistrationForm {
    $form=$this->userRegistrationFormFactory->create();
    $form->onFinished[]=function()use($form){
      $values=$form->getValues('array');
      try{
        //po registraci uživatele rovnou i přihlásíme
        $this->user->login($values['email'],$values['password']);
        $this->flashMessage('Vítejte v aplikaci nástěnky :)');
      }catch (\Exception $e){
        $this->flashMessage('Při registraci se vyskytla chyba','error');
      }
      $this->redirect('Homepage:default');
    };
    $form->onCancel[]=function()use($form){
      $this->redirect('Homepage:default');
    };
    return $form;
  }

  #region injections
  public function injectUsersFacade(UsersFacade $usersFacade):void {
    $this->usersFacade=$usersFacade;
  }

  public function injectUserLoginFormFactory(UserLoginFormFactory $userLoginFormFactory):void {
    $this->userLoginFormFactory=$userLoginFormFactory;
  }

  public function injectUserRegistrationFormFactory(UserRegistrationFormFactory $userRegistrationFormFactory):void {
    $this->userRegistrationFormFactory=$userRegistrationFormFactory;
  }
  #endregion injections
}
