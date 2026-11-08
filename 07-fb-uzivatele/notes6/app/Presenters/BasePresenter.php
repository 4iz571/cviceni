<?php


namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;

/**
 * Class BasePresenter
 * @package App\Presenters
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
        $this->redirect('User:login');//tady by bylo fajn uložit původní požadavek
      }
    }
  }

}