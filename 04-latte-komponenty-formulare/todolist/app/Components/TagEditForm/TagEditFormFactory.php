<?php

namespace App\Components\TagEditForm;

/**
 * Interface TagEditFormFactory
 * @package App\Components\TagEditForm
 */
interface TagEditFormFactory{

  public function create():TagEditForm;

}