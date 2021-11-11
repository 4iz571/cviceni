<?php

namespace App\Components\NoteEditForm;

/**
 * Interface NoteEditFormFactory
 * @package App\Components\NoteEditForm
 */
interface NoteEditFormFactory{

  public function create():NoteEditForm;

}