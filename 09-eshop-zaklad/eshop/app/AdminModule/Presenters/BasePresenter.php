<?php

namespace App\AdminModule\Presenters;

use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Security\User;

/**
 * Class BasePresenter
 * @package App\AdminModule\Presenters
 * @property-read User $user
 * @property-read DefaultTemplate $template
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {

  /**
   * @throws ForbiddenRequestException
   * @throws AbortException
   */
  protected function startup():void {
    parent::startup();
    $presenterName = $this->getRequest()->getPresenterName();
    $action = $this->getRequest()->getParameter('action') ?? '';

    if (!$this->user->isAllowed($presenterName,$action)){
      if ($this->user->isLoggedIn()){
        throw new ForbiddenRequestException();
      }else{
        $this->flashMessage('Pro zobrazení požadovaného obsahu se musíte přihlásit!','warning');
        //uložíme původní požadavek - předáme ho do persistentní proměnné v UserPresenteru
        $this->redirect(':Front:User:login', ['backlink' => $this->storeRequest()]);
      }
    }
  }

}