<?php

namespace App\Presenters;

use App\Components\NoteEditForm\NoteEditForm;
use App\Components\NoteEditForm\NoteEditFormFactory;
use App\Model\Facades\NotesFacade;
use Nette;

class HomepagePresenter extends BasePresenter{
  private NotesFacade $notesFacade;
  private NoteEditFormFactory $noteEditFormFactory;

  /**
   * Akce vykreslující přehled příspěvků
   * @param int|null $category
   */
  public function renderDefault(?int $category=null):void {
    $this->template->notes=$this->notesFacade->findNotes($category);
  }

  /**
   * Akce pro úpravu poznámky
   * @param int $id
   * @throws Nette\Application\BadRequestException
   */
  public function renderEdit(int $id):void {
    try{
      $note=$this->notesFacade->getNote($id);
    }catch (\Exception $e){
      throw new Nette\Application\BadRequestException('Požadovaná poznámka nebyla nalezena');
    }
    /** @var NoteEditForm $form */
    $form=$this->getComponent('noteEditForm');
    $form->setDefaults($note);
  }

  /**
   * Akce pro smazání poznámky
   * @param int $id
   * @throws Nette\Application\AbortException
   * @throws Nette\Application\BadRequestException
   * @throws \LeanMapper\Exception\InvalidStateException
   */
  public function actionDelete(int $id):void {
    try{
      $note=$this->notesFacade->getNote($id);
    }catch (\Exception $e){
      throw new Nette\Application\BadRequestException('Požadovaná poznámka nebyla nalezena');
    }
    $this->notesFacade->deleteNote($note);
    $this->redirect('default');
  }

  /**
   * Formulář pro editaci poznámek
   * @return NoteEditForm
   */
  protected function createComponentNoteEditForm():NoteEditForm {
    $form = $this->noteEditFormFactory->create();
    $form->onCancel[]=function(){
      $this->redirect('default');
    };
    $form->onFinished[]=function(){
      $this->redirect('default');
    };
    $form->onFailed[]=function($message=''){
      if (!empty($message)){
        $this->flashMessage($message,'error');
      }
      $this->redirect('default');
    };
    return $form;
  }

  #region injections
  public function injectNotesFacade(NotesFacade $notesFacade):void {
    $this->notesFacade=$notesFacade;
  }

  public function injectNoteEditFormFactory(NoteEditFormFactory $noteEditFormFactory):void {
    $this->noteEditFormFactory=$noteEditFormFactory;
  }
  #endregion injections
}
