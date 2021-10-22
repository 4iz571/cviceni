<?php

namespace App\Components\TodoEditForm;

/**
 * Interface TodoEditFormFactory
 * @package App\Components\TodoEditForm
 */
interface TodoEditFormFactory{

  public function create():TodoEditForm;

}