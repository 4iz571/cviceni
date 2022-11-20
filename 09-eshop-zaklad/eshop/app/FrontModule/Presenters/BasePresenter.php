<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\UserLoginControl\UserLoginControl;
use App\FrontModule\Components\UserLoginControl\UserLoginControlFactory;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;

/**
 * Class BasePresenter
 * @package App\FrontModule\Presenters
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
  private UserLoginControlFactory $userLoginControlFactory;

  /**
   * @throws ForbiddenRequestException
   * @throws AbortException
   */
  protected function startup():void {
    parent::startup();
    $presenterName = $this->request->presenterName;
    $action = !empty($this->request->parameters['action'])?$this->request->parameters['action']:'';

    if (!$this->user->isAllowed($presenterName,$action)){
      if ($this->user->isLoggedIn()){
        throw new ForbiddenRequestException();
      }else{
        $this->flashMessage('Pro zobrazení požadovaného obsahu se musíte přihlásit!','warning');
        //uložíme původní požadavek - předáme ho do persistentní proměnné v UserPresenteru
        $this->redirect('User:login', ['backlink' => $this->storeRequest()]);
      }
    }
  }

  /**
   * Komponenta pro zobrazení údajů o aktuálním uživateli (přihlášeném či nepřihlášeném)
   * @return UserLoginControl
   */
  public function createComponentUserLogin():UserLoginControl {
    return $this->userLoginControlFactory->create();
  }

  #region injections
  public function injectUserLoginControlFactory(UserLoginControlFactory $userLoginControlFactory):void {
    $this->userLoginControlFactory=$userLoginControlFactory;
  }
  #endregion injections
}