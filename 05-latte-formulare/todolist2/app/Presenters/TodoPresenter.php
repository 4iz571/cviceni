<?php

namespace App\Presenters;

use App\Model\Facades\TodosFacade;
use Nette\Application\UI\Presenter;

/**
 * Class TodoPresenter
 * @package App\Presenters
 */
class TodoPresenter extends Presenter{
  private TodosFacade $todosFacade;

  /**
   * Akce pro výpis úkolů
   */
  public function renderDefault():void {
    //TODO tady to bude chtít v rámci plnění úkolů na cvičení nějaké změny
    $this->template->todos = $this->todosFacade->findTodos();
  }

  #region injections
  public function injectTodosFacade(TodosFacade $todosFacade):void {
    $this->todosFacade=$todosFacade;
  }
  #endregion injections
}