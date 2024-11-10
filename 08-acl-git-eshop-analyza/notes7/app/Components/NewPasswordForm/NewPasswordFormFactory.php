<?php

namespace App\Components\NewPasswordForm;

/**
 * Interface NewPasswordFormFactory
 * @package App\Components\NewPasswordForm
 */
interface NewPasswordFormFactory{

  public function create():NewPasswordForm;

}