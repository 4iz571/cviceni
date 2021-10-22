<?php

namespace App\Presenters;

use App\Model\Facades\TodosFacade;
use Nette\Application\UI\Presenter;

/**
 * Class TodoPresenter
 * @package App\Presenters
 */
class TodoPresenter extends Presenter{
  /** @var TodosFacade $todosFacade*/
  private $todosFacade;

  /**
   * Akce pro výpis úkolů
   */
  public function renderDefault(){
    //TODO tady to bude chtít v rámci plnění úkolů na cvičení nějaké změny
    $this->template->todos = $this->todosFacade->findTodos();
  }


  public function injectTodosFacade(TodosFacade $todosFacade){
    $this->todosFacade=$todosFacade;
  }
}