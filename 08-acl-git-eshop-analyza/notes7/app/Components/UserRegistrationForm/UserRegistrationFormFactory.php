<?php

namespace App\Components\UserRegistrationForm;

/**
 * Interface UserRegistrationFormFactory
 * @package App\Components\UserRegistrationForm
 */
interface UserRegistrationFormFactory{

  public function create():UserRegistrationForm;

}