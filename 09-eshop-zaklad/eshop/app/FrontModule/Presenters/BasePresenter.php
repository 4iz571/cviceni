<?php

namespace App\FrontModule\Presenters;

use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;

/**
 * Class BasePresenter
 * @package App\FrontModule\Presenters
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {

  /**
   * @throws ForbiddenRequestException
   * @throws AbortException
   */
  protected function startup(){
    parent::startup();
    $presenterName = $this->request->presenterName;
    $action = !empty($this->request->parameters['action'])?$this->request->parameters['action']:'';

    if (!$this->user->isAllowed('Front:'.$presenterName,$action)){
      if ($this->user->isLoggedIn()){
        throw new ForbiddenRequestException();
      }else{
        $this->flashMessage('Pro zobrazení požadovaného obsahu se musíte přihlásit!','warn');
        //uložíme původní požadavek - předáme ho do persistentní proměnné v UserPresenteru
        $this->redirect('User:login', ['backlink' => $this->storeRequest()]);
      }
    }
  }

}