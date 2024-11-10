<?php

namespace App\Components\UserLoginForm;

/**
 * Interface UserLoginFormFactory
 * @package App\Components\UserLoginForm
 */
interface UserLoginFormFactory{

  public function create():UserLoginForm;

}