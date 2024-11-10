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
use App\Components\UserSecretCodeForm\UserSecretCodeForm;
use App\Components\UserSecretCodeForm\UserSecretCodeFormFactory;
use App\Model\Api\Facebook\FacebookApi;
use App\Model\Facades\UsersFacade;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Nette;
use Nette\Application\BadRequestException;
use PragmaRX\Google2FA\Google2FA;

/**
 * Class UserPresenter - presenter pro akce týkající se uživatelů
 * @package App\Presenters
 * @property string $backlink
 */
class UserPresenter extends BasePresenter{
  private UsersFacade $usersFacade;
  private UserLoginFormFactory $userLoginFormFactory;
  private UserRegistrationFormFactory $userRegistrationFormFactory;
  private ForgottenPasswordFormFactory $forgottenPasswordFormFactory;
  private NewPasswordFormFactory $newPasswordFormFactory;
  private UserSecretCodeFormFactory $userSecretCodeFormFactory;
  private FacebookApi $facebookApi;
  /** @persistent */
  public string $backlink = '';
  /**
   * Akce pro nastavení 2FA
   * @throws \Exception
   */
  public function renderConfig2fa():void{
    $currentUser=$this->usersFacade->getUser($this->user->id);
    $this->template->currentUser=$currentUser;

    if (empty($currentUser->secretCode)){
      #region dáme uživateli možnost nastavit si 2FA
      $form = $this->getComponent('newUserSecretCodeForm');

      //vygenerujeme nový secret kód
      $google2FA = new Google2FA();

      if ($form->isSubmitted()){
        $newSecretCode=$form->getValues('array')['new_secret_code'];
      }else{
        $newSecretCode=$google2FA->generateSecretKey();
        $form->setDefaults([
          'new_secret_code'=>$newSecretCode
        ]);
      }

      #region sestavení URL a vygenerování QR kódu
      $qrCodeUrl=$google2FA->getQRCodeUrl(
        'NOTES6',//TODO tohle by bylo vhodnější mít v configu
        $currentUser->email,
        $newSecretCode
      );

      $qrCode = new QrCode($qrCodeUrl);
      $qrCodeImage = (new PngWriter())->write($qrCode)->getDataUri();
      #endregion sestavení URL a vygenerování QR kódu

      $this->template->qrCodeImage = $qrCodeImage;
      #endregion dáme uživateli možnost nastavit si 2FA
    }
  }

  /**
   * Akce pro smazání 2FA
   * @throws Nette\Application\AbortException
   */
  public function actionUnset2fa():void {
    $currentUser=$this->usersFacade->getUser($this->user->id);
    $currentUser->secretCode=null;
    $this->usersFacade->saveUser($currentUser);
    $this->flashMessage('Váš účet není aktuálně chráněn jednorázovým kódem v rámci 2FA.');

    $this->redirect('config2fa');
  }

  /**
   * Akce pro ověření 2FA - kontrola, jestli je pro daného uživatele vyžadována
   * @throws Nette\Application\AbortException
   */
  public function actionLogin2fa():void {
    if (!$this->user->isInRole('require2fa')){
      $this->redirect('Homepage:default');
    }
  }
  
  /**
   * Akce pro odhlášení uživatele
   * @throws Nette\Application\AbortException
   */
  public function actionLogout():void{
    if ($this->user->isLoggedIn()){
      $this->user->logout();
    }
    $this->redirect('Homepage:default');
  }

  /**
   * Akce pro přihlášení - pokud už je uživatel přihlášen, tak ho jen přesměrujeme na homepage
   * @throws Nette\Application\AbortException
   */
  public function actionLogin():void{
    if ($this->user->isLoggedIn()){
      //obnovíme uložený požadavek - pokud se to nepovede, pokračujeme přesměrováním
      $this->restoreRequest($this->backlink);
      $this->redirect('Homepage:default');
    }
  }

  /**
   * Akce pro registraci - pokud už je uživatel přihlášen, tak ho jen přesměrujeme na homepage
   * @throws Nette\Application\AbortException
   */
  public function actionRegister():void{
    if ($this->user->isLoggedIn()){
      $this->redirect('Homepage:default');
    }
  }

  /**
   * Akce pro přihlášení pomocí Facebooku
   * @param bool $callback
   */
  public function actionFacebookLogin(bool $callback=false):void{
    if ($callback){
      #region návrat z Facebooku
      try{
        $facebookUser = $this->facebookApi->getFacebookUser(); //v proměnné $facebookUser máme facebookId, email a jméno uživatele => jdeme jej přihlásit

        //necháme si vytvořit identitu uživatele
        $userUdentity = $this->usersFacade->getFacebookUserIdentity($facebookUser);

        //přihlásíme uživatele
        $this->user->login($userUdentity);

      }catch (\Exception $e){
        $this->flashMessage('Přihlášení pomocí Facebooku se nezdařilo.','error');
        $this->redirect('Homepage:default');
      }

      //obnovíme uložený požadavek - pokud se to nepovede, pokračujeme přesměrováním
      $this->restoreRequest($this->backlink);
      $this->redirect('Homepage:default');
      #endregion návrat z Facebooku
    }else{
      #region přesměrování na přihlášení pomocí Facebooku
      $backlink = $this->link('//User:facebookLogin',['callback'=>true]);
      $facebookLoginLink = $this->facebookApi->getLoginUrl($backlink);
      $this->redirectUrl($facebookLoginLink);
      #endregion přesměrování na přihlášení pomocí Facebooku
    }
  }
  
  /**
   * Akce pro zadání nového hesla v rámci jeho obnovy
   * @param int $user
   * @param string $code
   * @throws BadRequestException
   * @throws Nette\Application\AbortException
   */
  public function renderRenewPassword(int $user, string $code):void{
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

      //obnovíme uložený požadavek - pokud se to nepovede, pokračujeme přesměrováním
      $this->restoreRequest($this->backlink);
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

  /**
   * Formulář pro nastavení 2FA secret code pro uživatele
   * @return UserSecretCodeForm
   */
  protected function createComponentNewUserSecretCodeForm():UserSecretCodeForm {
    $form = $this->userSecretCodeFormFactory->create();
    $form->createSubcomponents(true);

    $form->onFinished[]=function(string $message = ''){
      if (!empty($message)){
        $this->flashMessage($message);
      }

      $this->redirect('Homepage:default');
    };

    return $form;
  }

  /**
   * Formulář pro ověření 2FA secret code pro uživatele
   * @return UserSecretCodeForm
   */
  protected function createComponentValidateUserSecretCodeForm():UserSecretCodeForm {
    $form = $this->userSecretCodeFormFactory->create();
    $form->createSubcomponents(false);

    $form->onFinished[]=function(string $message = ''){
      if (!empty($message)){
        $this->flashMessage($message);
      }

      $this->redirect('Homepage:default');
    };

    return $form;
  }

  #region injections
  public function injectUsersFacade(UsersFacade $usersFacade):void{
    $this->usersFacade=$usersFacade;
  }

  public function injectUserLoginFormFactory(UserLoginFormFactory $userLoginFormFactory):void{
    $this->userLoginFormFactory=$userLoginFormFactory;
  }

  public function injectUserRegistrationFormFactory(UserRegistrationFormFactory $userRegistrationFormFactory):void{
    $this->userRegistrationFormFactory=$userRegistrationFormFactory;
  }

  public function injectForgottenPasswordFormFactory(ForgottenPasswordFormFactory $forgottenPasswordFormFactory):void{
    $this->forgottenPasswordFormFactory=$forgottenPasswordFormFactory;
  }

  public function injectNewPasswordFormFactory(NewPasswordFormFactory $newPasswordFormFactory):void{
    $this->newPasswordFormFactory=$newPasswordFormFactory;
  }

  public function injectUserSecretCodeFormFactory(UserSecretCodeFormFactory $userSecretCodeFormFactory):void {
    $this->userSecretCodeFormFactory=$userSecretCodeFormFactory;
  }

  public function injectFacebookApi( FacebookApi $facebookApi):void{
    $this->facebookApi=$facebookApi;
  }
  #endregion injections
}
