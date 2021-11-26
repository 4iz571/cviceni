<?php

namespace App\FrontModule\Components\UserLoginControl;

/**
 * Interface UserLoginControlFactory
 * @package App\FrontModule\Components\UserLoginControl
 */
interface UserLoginControlFactory{

  public function create():UserLoginControl;

}