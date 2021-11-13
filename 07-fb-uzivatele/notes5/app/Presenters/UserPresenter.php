<?php

namespace App\Presenters;

use App\Components\ForgottenPasswordForm\ForgottenPasswordForm;
use App\Components\ForgottenPasswordForm\ForgottenPasswordFormFactory;
use App\Components\NewPasswordForm\NewPasswordForm;
use App\Components\NewPasswordForm\NewPasswordFormFactory;
use App\Components\UserLoginForm\UserLoginForm;
use App\Components\UserLoginForm\UserLoginFormFactory;
use App\Components\UserRegistrationForm\UserRegistrationForm;
use App\Components\UserRegistrationForm\UserRegistrationFormFactory;
use App\Model\Api\Facebook\FacebookApi;
use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\BadRequestException;

/**
 * Class UserPresenter - presenter pro akce týkající se uživatelů
 * @package App\Presenters
 */
class UserPresenter extends BasePresenter{
  /** @var UsersFacade $usersFacade */
  private $usersFacade;
  /** @var UserLoginFormFactory $userLoginFormFactory */
  private $userLoginFormFactory;
  /** @var UserRegistrationFormFactory $userRegistrationFormFactory */
  private $userRegistrationFormFactory;
  /** @var ForgottenPasswordFormFactory $forgottenPasswordFormFactory */
  private $forgottenPasswordFormFactory;
  /** @var NewPasswordFormFactory $newPasswordFormFactory */
  private $newPasswordFormFactory;
/** @var FacebookApi $facebookApi */
  private $facebookApi;
  
  /**
   * Akce pro odhlášení uživatele
   * @throws Nette\Application\AbortException
   */
  public function actionLogout(){
    if ($this->user->isLoggedIn()){
      $this->user->logout();
    }
    $this->redirect('Homepage:default');
  }

  /**
   * Akce pro přihlášení - pokud už je uživatel přihlášen, tak ho jen přesměrujeme na homepage
   * @throws Nette\Application\AbortException
   */
  public function actionLogin(){
    if ($this->user->isLoggedIn()){
      $this->redirect('Homepage:default');
    }
  }

  /**
   * Akce pro registraci - pokud už je uživatel přihlášen, tak ho jen přesměrujeme na homepage
   * @throws Nette\Application\AbortException
   */
  public function actionRegister(){
    if ($this->user->isLoggedIn()){
      $this->redirect('Homepage:default');
    }
  }

  /**
   * Akce pro přihlášení pomocí Facebooku
   * @param bool $callback
   */
  public function actionFacebookLogin(bool $callback=false){

    //TODO tady bude přihlášení pomocí App\Model\Api\Facebook\FacebookApi
    //proměnnou $callback použijeme pro rozlišení, zda jde o první zaslání požadavku, nebo o návrat z FB

  }
  
  /**
   * Akce pro zadání nového hesla v rámci jeho obnovy
   * @param int $user
   * @param string $code
   * @throws BadRequestException
   * @throws Nette\Application\AbortException
   */
  public function renderRenewPassword(int $user, string $code){
    if ($this->usersFacade->isValidForgottenPasswordCode($user, $code)){
      #region odkaz na obnovu hesla byl platný
      try{
        $userEntity=$this->usersFacade->getUser($user);
      }catch (\Exception $e){
        throw new BadRequestException('Požadovaný uživatel neexistuje.','error');
      }

      $form = $this->getComponent('newPasswordForm');
      $form->setDefaults($userEntity);
      #endregion odkaz na obnovu hesla byl platný
    }else{
      #region odkaz již není platný
      $this->flashMessage('Odkaz na změnu hesla již není platný. Pokud potřebujete heslo obnovit, zašlete žádost znovu.','error');
      $this->redirect('Homepage:default');
      #endregion odkaz již není platný
    }
  }

  /**
   * Formulář pro přihlášení existujícího uživatele
   * @return UserLoginForm
   */
  protected function createComponentUserLoginForm():UserLoginForm{
    $form=$this->userLoginFormFactory->create();
    $form->onFinished[]=function()use($form){
      $values=$form->getValues('array');
      try{
        $this->user->login($values['email'],$values['password']);
        //po přihlášení uživatele smažeme jeho kódy na obnovu hesla
        $this->usersFacade->deleteForgottenPasswordsByUser($this->user->id);
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
  protected function createComponentUserRegistrationForm():UserRegistrationForm{
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

  /**
   * Formulář pro obnovu zapomenutého hesla
   * @return ForgottenPasswordForm
   */
  protected function createComponentForgottenPasswordForm():ForgottenPasswordForm{
    $form=$this->forgottenPasswordFormFactory->create();
    $form->onFinished[]=function($message=''){
      if (!empty($message)){
        $this->flashMessage($message);
      }
      $this->redirect('login');
    };
    $form->onCancel[]=function()use($form){
      $this->redirect('login');
    };
    return $form;
  }

  /**
   * Formulář pro zadání nového hesla
   * @return NewPasswordForm
   */
  protected function createComponentNewPasswordForm():NewPasswordForm{
    $form=$this->newPasswordFormFactory->create();
    $form->onFinished[]=function($message=''){
      if (!empty($message)){
        $this->flashMessage($message);
      }
      $this->redirect('login');
    };
    $form->onFailed[]=function($message=''){
      if (!empty($message)){
        $this->flashMessage($message);
      }
      $this->redirect('Homepage:default');
    };
    $form->onCancel[]=function()use($form){
      $this->redirect('Homepage:default');
    };
    return $form;
  }

  #region injections
  public function injectUsersFacade(UsersFacade $usersFacade){
    $this->usersFacade=$usersFacade;
  }

  public function injectUserLoginFormFactory(UserLoginFormFactory $userLoginFormFactory){
    $this->userLoginFormFactory=$userLoginFormFactory;
  }

  public function injectUserRegistrationFormFactory(UserRegistrationFormFactory $userRegistrationFormFactory){
    $this->userRegistrationFormFactory=$userRegistrationFormFactory;
  }

  public function injectForgottenPasswordFormFactory(ForgottenPasswordFormFactory $forgottenPasswordFormFactory){
    $this->forgottenPasswordFormFactory=$forgottenPasswordFormFactory;
  }

  public function injectNewPasswordFormFactory(NewPasswordFormFactory $newPasswordFormFactory){
    $this->newPasswordFormFactory=$newPasswordFormFactory;
  }
/*TODO zaregistrujte službu FacebookApi v configu a poté povolte tuto metodu
  public function injectFacebookApi( FacebookApi $facebookApi){
    $this->facebookApi=$facebookApi;
  }*/
  #endregion injections
}
