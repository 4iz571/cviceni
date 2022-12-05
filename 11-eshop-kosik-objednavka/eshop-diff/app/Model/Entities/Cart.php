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
 */
class Cart extends Entity{

  public function updateCartItems(){
    $this->row->cleanReferencingRowsCache('cart_item'); //smažeme cache, aby se položky v košíku znovu načetly z DB bez nutnosti načtení celého košíku
  }

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