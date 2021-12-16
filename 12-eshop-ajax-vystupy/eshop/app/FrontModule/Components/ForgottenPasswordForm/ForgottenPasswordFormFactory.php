<?php

namespace App\FrontModule\Components\ForgottenPasswordForm;

/**
 * Interface ForgottenPasswordFormFactory
 * @package App\FrontModule\Components\UserLoginForm
 */
interface ForgottenPasswordFormFactory{

  public function create():ForgottenPasswordForm;

}