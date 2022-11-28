<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\CartControl\CartControl;
use App\FrontModule\Components\CartControl\CartControlFactory;
use App\FrontModule\Components\UserLoginControl\UserLoginControl;
use App\FrontModule\Components\UserLoginControl\UserLoginControlFactory;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;

/**
 * Class BasePresenter
 * @package App\FrontModule\Presenters
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
  /** @var UserLoginControlFactory $userLoginControlFactory */
  private $userLoginControlFactory;
  /** @var CartControlFactory $cartControlFactory*/
  private $cartControlFactory;

  /**
   * @throws ForbiddenRequestException
   * @throws AbortException
   */
  protected function startup(){
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

  /**
   * Komponenta košíku
   * @return CartControl
   */
  public function createComponentCart():CartControl {
    return $this->cartControlFactory->create();
  }

  #region injections
  public function injectUserLoginControlFactory(UserLoginControlFactory $userLoginControlFactory):void {
    $this->userLoginControlFactory=$userLoginControlFactory;
  }

  public function injectCartControlFactory(CartControlFactory $cartControlFactory):void {
    $this->cartControlFactory=$cartControlFactory;
  }
  #endregion injections
}