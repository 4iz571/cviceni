<?php

namespace App\FrontModule\Components\ProductCartForm;

/**
 * Interface ProductCartFormFactory
 * @package App\FrontModule\Components\ProductCartForm
 */
interface ProductCartFormFactory{

  public function create():ProductCartForm;

}