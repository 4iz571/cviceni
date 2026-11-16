<?php


namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette\Security\User;

/**
 * Class BasePresenter
 * @package App\Presenters
 * @property-read User $user
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {

  /**
   * @throws ForbiddenRequestException
   * @throws AbortException
   */
  protected function startup():void{
    parent::startup();
    $presenterName = $this->getRequest()->getPresenterName();
    $action = $this->getRequest()->getParameter('action') ?? '';

    if (!$this->getUser()->isAllowed($presenterName,$action)){
      if ($this->getUser()->isLoggedIn()){
        if ($this->getUser()->isInRole('require2fa')){
          //u tohoto uživatele vyžadujeme ověření 2FA
          $this->redirect('User:login2fa');
        }
        throw new ForbiddenRequestException();
      }else{
        $this->flashMessage('Pro zobrazení požadovaného obsahu se musíte přihlásit!','warn');
        //uložíme původní požadavek - předáme ho do persistentní proměnné v UserPresenteru
        $this->redirect('User:login', ['backlink' => $this->storeRequest()]);
      }
    }
  }

}