<?php

declare(strict_types=1);
namespace App\Presenters;

use App\Components\TagEditForm\TagEditForm;
use App\Components\TagEditForm\TagEditFormFactory;
use App\Model\Facades\TagsFacade;
use Nette\Application\BadRequestException;

/**
 * Class TagPresenter
 * @package App\Presenters
 */
class TagPresenter extends \Nette\Application\UI\Presenter {
  private TagsFacade $tagsFacade;
  private TagEditFormFactory $tagEditFormFactory;

  /**
   * Akce pro zobrazení přehledu tagů
   */
  public function renderDefault():void {
    $this->template->tags=$this->tagsFacade->findTags();
  }

  /**
   * Akce pro smazání tagu
   * @param int $id
   * @throws \Nette\Application\AbortException
   */
  public function actionDelete(int $id):void {
    try{
      $tag=$this->tagsFacade->getTag($id);
    }catch (\Exception $e){
      $this->flashMessage('Požadovaný tag nebyl nalezen.','error');
    }
    if ($this->tagsFacade->deleteTag($tag)){
      $this->flashMessage('Tag byl smazán');
    }else{
      $this->flashMessage('Tag se nepodařilo smazat.','error');
    }
    $this->redirect('default');
  }

  /**
   * Akce pro úpravu jednoho tagu
   * @param int $id
   * @throws \Nette\Application\BadRequestException
   */
  public function renderEdit(int $id):void {
    try{
      $tag=$this->tagsFacade->getTag($id);
    }catch (\Exception $e){
      throw new BadRequestException('Požadovaný tag nebyl nalezen');
    }
    $form=$this->getComponent('tagEditForm');
    $form->setDefaults($tag);
    $this->template->tag=$tag;
  }

  /**
   * Formulář na editaci kategorií
   * @return TagEditForm
   */
  public function createComponentTagEditForm():TagEditForm {
    //pomocí továrničky vytvoříme instanci formuláře
    $form=$this->tagEditFormFactory->create();
    //k formuláři přiřadíme reakce po jeho dokončení/zrušení
    $form->onFinished[]=function(string $message=''){
      if (!empty($message)){
        $this->flashMessage($message);
      }
      $this->redirect('default');
    };
    $form->onFailed[]=function(string $message=''){
      if (!empty($message)){
        $this->flashMessage($message,'error');
      }
      $this->redirect('default');
    };
    $form->onCancel[]=function(){
      $this->redirect('default');
    };
    return $form;
  }

  #region injections
  public function injectTagsFacade(TagsFacade $tagsFacade):void {
    $this->tagsFacade=$tagsFacade;
  }

  public function injectTagEditFormFactory(TagEditFormFactory $tagEditFormFactory):void {
    $this->tagEditFormFactory=$tagEditFormFactory;
  }
  #endregion injections
}
