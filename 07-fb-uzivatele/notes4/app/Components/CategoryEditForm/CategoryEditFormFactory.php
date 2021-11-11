<?php

namespace App\Components\CategoryEditForm;

/**
 * Interface CategoryEditFormFactory
 * @package App\Components\CategoryEditForm
 */
interface CategoryEditFormFactory{

  public function create():CategoryEditForm;

}