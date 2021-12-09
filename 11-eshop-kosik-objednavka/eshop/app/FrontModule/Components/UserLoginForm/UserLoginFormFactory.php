<?php

namespace App\FrontModule\Components\UserLoginForm;

/**
 * Interface UserLoginFormFactory
 * @package App\FrontModule\Components\UserLoginForm
 */
interface UserLoginFormFactory{

  public function create():UserLoginForm;

}