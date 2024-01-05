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
  protected function startup(){
    parent::startup();
    $presenterName = $this->request->presenterName;
    $action = !empty($this->request->parameters['action'])?$this->request->parameters['action']:'';

    if (!$this->user->isAllowed($presenterName,$action)){
      if ($this->user->isLoggedIn()){
        throw new ForbiddenRequestException();
      }else{
        $this->flashMessage('Pro zobrazení požadovaného obsahu se musíte přihlásit!','warn');
        $this->redirect('User:login');//tady by bylo fajn uložit původní požadavek
      }
    }
  }

}