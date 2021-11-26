<?php

namespace App\FrontModule\Components\UserLoginControl;

use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Nette\Security\User;

/**
 * Class UserLoginControl
 * @package App\FrontModule\Components\UserLoginControl
 */
class UserLoginControl extends Control{
  /** @var User $user */
  private $user;

  /**
   * Akce renderující šablonu s odkazem pro zobrazení harmonogramu na desktopu
   * @param array $params = []
   */
  public function render($params=[]){
    $template=$this->prepareTemplate('default');
    $template->user=$this->user;
    $template->class=(!empty($params['class'])?$params['class']:'');
    $template->render();
  }

  /**
   * Signál pro přihlášení uživatele s uložením requestu
   * @throws \Nette\Application\AbortException
   */
  public function handleLogin(){
    if ($this->user->isLoggedIn()){
      $this->presenter->redirect('this');
    }else{
      $this->presenter->redirect(':Front:User:login',['backlink'=>$this->presenter->storeRequest()]);
    }
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function handleLogout(){
    if (!$this->user->isLoggedIn()){
      $this->presenter->redirect('this');
    }else{
      $this->presenter->redirect(':Front:User:logout');
    }
  }

  /**
   * UserLoginControl constructor.
   * @param User $user
   */
  public function __construct(User $user){
    $this->user=$user;
  }

  /**
   * Metoda vytvářející šablonu komponenty
   * @param string $templateName=''
   * @return Template
   */
  private function prepareTemplate(string $templateName=''):Template{
    $template=$this->template;
    if (!empty($templateName)){
      $template->setFile(__DIR__.'/templates/'.$templateName.'.latte');
    }
    return $template;
  }

}