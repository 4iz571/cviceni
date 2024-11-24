<?php

namespace App\Model\Facades;

use App\Model\Entities\Cart;
use App\Model\Entities\CartItem;
use App\Model\Entities\User;
use App\Model\Repositories\CartItemRepository;
use App\Model\Repositories\CartRepository;
use Dibi\DateTime;

class CartFacade{
  private CartRepository $cartRepository;
  private CartItemRepository $cartItemRepository;

  /**
   * Metoda vracející košík podle cartId
   * @param int $id
   * @return Cart
   * @throws \Exception
   */
  public function getCartById(int $id):Cart {
    return $this->cartRepository->find($id);
  }

  /**
   * Metoda vracející košík konkrétního uživatele
   * @param User|int $user
   * @return Cart
   * @throws \Exception
   */
  public function getCartByUser($user):Cart {
    if ($user instanceof User){
      $user=$user->userId;
    }
    return $this->cartRepository->findBy(['user_id'=>$user]);
  }

  /**
   * Metoda pro smazání košíku konkrétního uživatele
   * @param User|int $user
   */
  public function deleteCartByUser($user):void {
    try{
      $this->cartRepository->delete($this->getCartByUser($user));
    }catch (\Exception $e){}
  }

  /**
   * Metoda pro smazání starých košíků
   */
  public function deleteOldCarts():void {
    try{
      $this->cartRepository->deleteOldCarts();
    }catch (\Exception $e){}
  }

  /**
   * Metoda pro uložení položky v košíku
   * @param CartItem $cartItem
   */
  public function saveCartItem(CartItem $cartItem):void {
    $this->cartItemRepository->persist($cartItem);
  }

  /**
   * Metoda pro uložení košíku, automaticky aktualizuje informaci o jeho poslední změně
   * @param Cart $cart
   */
  public function saveCart(Cart $cart):void {
    $cart->lastModified = new DateTime();
    $this->cartRepository->persist($cart);
  }

  /**
   * CartFacade constructor.
   * @param CartRepository $cartRepository
   * @param CartItemRepository $cartItemRepository
   */
  public function __construct(CartRepository $cartRepository, CartItemRepository $cartItemRepository){
    $this->cartRepository=$cartRepository;
    $this->cartItemRepository=$cartItemRepository;
  }
}