<?php


namespace App\Model\Entities;


use LeanMapper\Entity;

/**
 * Class CartItem
 * @package App\Model\Entities
 * @property int $cartItem
 * @property Product $product m:hasOne
 * @property Cart $cart m:hasOne
 * @property int $count
 */
class CartItem extends Entity{

}