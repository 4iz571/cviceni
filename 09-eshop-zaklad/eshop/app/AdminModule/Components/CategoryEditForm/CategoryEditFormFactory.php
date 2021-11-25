<?php

namespace App\AdminModule\Components\CategoryEditForm;

/**
 * Interface CategoryEditFormFactory
 * @package App\AdminModule\Components\CategoryEditForm
 */
interface CategoryEditFormFactory{

  public function create():CategoryEditForm;

}