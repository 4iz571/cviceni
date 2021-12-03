<?php

namespace App\FrontModule\Components\CartControl;

use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Nette\Http\Session;
use Nette\Security\User;

/**
 * Class CartControl
 * @package App\FrontModule\Components\CartControl
 */
class CartControl extends Control{
  /** @var User $user */
  private $user;

  /**
   * Akce renderující šablonu s odkazem pro zobrazení harmonogramu na desktopu
   * @param array $params = []
   */
  public function render($params=[]):void {
    $template=$this->prepareTemplate('default');
    $template->render();
  }


  /**
   * UserLoginControl constructor.
   * @param User $user
   * @param Session $session
   */
  public function __construct(User $user, Session $session){
    $this->user=$user;
    $cartSession=$session->getSection('cart');

    //TODO načtení košíku pro aktuálního uživatele či vytvoření nového
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