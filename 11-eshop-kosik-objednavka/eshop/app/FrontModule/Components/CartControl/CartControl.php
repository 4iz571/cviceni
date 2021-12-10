<?php

namespace App\FrontModule\Components\CartControl;

use App\Model\Entities\Cart;
use App\Model\Entities\Product;
use App\Model\Facades\CartFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security;

/**
 * Class CartControl
 * @package App\FrontModule\Components\CartControl
 */
class CartControl extends Control{
  /** @var Security\User $user */
  private $user;
  /** @var CartFacade $cartFacade */
  private $cartFacade;
  /** @var SessionSection */
  private $cartSession;
  /** @var Cart $cart */
  private $cart;

  /**
   * Akce renderující šablonu s odkazem pro zobrazení harmonogramu na desktopu
   * @param array $params = []
   */
  public function render($params=[]):void {
    $template=$this->prepareTemplate('default');
    $template->cart=$this->cart;
    $template->render();
  }

  /**
   * Metoda pro přidání produktu do košíku
   * @param Product $product
   */
  public function addToCart(Product $product){
    //TODO implementovat
  }

  /**
   * UserLoginControl constructor.
   * @param Security\User $user
   * @param Session $session
   * @param CartFacade $cartFacade
   */
  public function __construct(Security\User $user, Session $session, CartFacade $cartFacade){
    $this->user=$user;
    $this->cartFacade=$cartFacade;
    $this->cartSession=$session->getSection('cart');
    $this->cart=$this->prepareCart();
  }

  /**
   * Metoda pro smazání ID košíku ze session
   * TODO tuto metodu by bylo vhodné zavolat např. při odhlášení uživatele
   */
  public function unsetSessionCart():void {
    $this->cartSession->remove('cartId');
  }

  /**
   * Metoda pro přípravu košíku uloženého v DB
   */
  private function prepareCart():Cart {
    #region zkusíme najít košík podle ID ze session
    try {
      if ($cartId = $this->cartSession->get('cartId')){
        $cart = $this->cartFacade->getCartById((int)$cartId);
        //zkontrolujeme, jestli tu není košík od předchozího uživatele, nebo se nepřihlásil uživatel s prázdným košíkem (případně ho zahodíme)
        if (($cart->userId || empty($cart->items)) && $this->user->isLoggedIn() && ($cart->userId!=$this->user->id)){
          $cart=null;
        }
      }
    }catch (\Exception $e){
      /*košík se nepovedlo najít*/
    }
    #endregion zkusíme najít košík podle ID ze session
    #region vyřešíme vazbu košíku na uživatele, případně vytvoříme košík nový
    if ($this->user->isLoggedIn()){
      if ($cart){
        //přiřadíme do košíku načteného podle session vazbu na aktuálního uživatele
        $this->cartFacade->deleteCartByUser($this->user->id);
        $cart->userId=$this->user->id;
        $this->cartFacade->saveCart($cart);
      }else{
        //zkusíme najít košík podle ID uživatele - pokud ho nenajdeme, vytvoříme nový
        try{
          $cart=$this->cartFacade->getCartByUser($this->user->id);
        }catch (\Exception $e){
          /*košík nebyl pro daného uživatele nalezen*/
          $cart=new Cart();
          $cart->userId=$this->user->id;
          $this->cartFacade->saveCart($cart);
        }
      }
    }elseif(!$cart){
      //košík jsme zatím nijak nezvládli najít, vytvoříme nový prázdný
      $cart=new Cart();
      $this->cartFacade->saveCart($cart);
    }
    #endregion vyřešíme vazbu košíku na uživatele, případně vytvoříme košík nový

    //aktualizujeme ID košíku v session
    $this->cartSession->set('cartId',$cart->cartId);

    return $cart;
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