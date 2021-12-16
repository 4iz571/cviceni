<?php

namespace App\FrontModule\Components\NewPasswordForm;

/**
 * Interface NewPasswordFormFactory
 * @package App\FrontModule\Components\NewPasswordForm
 */
interface NewPasswordFormFactory{

  public function create():NewPasswordForm;

}