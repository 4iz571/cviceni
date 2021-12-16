<?php

namespace App\AdminModule\Components\ProductEditForm;

/**
 * Interface ProductEditFormFactory
 * @package App\AdminModule\Components\ProductEditForm
 */
interface ProductEditFormFactory{

  public function create():ProductEditForm;

}