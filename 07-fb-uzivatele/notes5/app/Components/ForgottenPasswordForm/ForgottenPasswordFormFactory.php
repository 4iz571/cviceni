<?php

namespace App\Components\ForgottenPasswordForm;

/**
 * Interface ForgottenPasswordFormFactory
 * @package App\Components\UserLoginForm
 */
interface ForgottenPasswordFormFactory{

  public function create():ForgottenPasswordForm;

}