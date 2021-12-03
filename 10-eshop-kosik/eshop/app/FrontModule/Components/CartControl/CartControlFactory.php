<?php

namespace App\FrontModule\Components\CartControl;

/**
 * Interface CartControlFactory
 * @package App\FrontModule\Components\CartControl
 */
interface CartControlFactory{

  public function create():CartControl;

}