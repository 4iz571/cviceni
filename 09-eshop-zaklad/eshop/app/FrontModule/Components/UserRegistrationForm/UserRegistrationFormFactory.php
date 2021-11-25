<?php

namespace App\FrontModule\Components\UserRegistrationForm;

/**
 * Interface UserRegistrationFormFactory
 * @package App\FrontModule\Components\UserRegistrationForm
 */
interface UserRegistrationFormFactory{

  public function create():UserRegistrationForm;

}