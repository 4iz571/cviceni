<?php

namespace App\Components\UserSecretCodeForm;

/**
 * Interface UserSecretCodeFormFactory
 * @package App\Components\UserSecretCodeForm
 */
interface UserSecretCodeFormFactory{

  public function create():UserSecretCodeForm;

}