<?php

namespace App\Model\Entities;

use Dibi\DateTime;
use LeanMapper\Entity;

/**
 * Class Cart
 * @package App\Model\Entities
 * @property int $cartId
 * @property int|null $userId = null
 * @property CartItem[] $items m:belongsToMany
 * @property DateTime|null $lastModified
 *
 * @method addToItems(CartItem $cartItem)
 * @method removeFromItems(CartItem $cartItem)
 * @method removeAllItems()
 */
class Cart extends Entity{

  public function getTotalCount():int {
    $result = 0;
    if (!empty($this->items)){
      foreach ($this->items as $item){
        $result+=$item->count;
      }
    }
    return $result;
  }

  public function getTotalPrice():float {
    $result=0;
    if (!empty($this->items)){
      foreach ($this->items as $item){
        $result+=$item->product->price*$item->count;
      }
    }
    return $result;
  }

}