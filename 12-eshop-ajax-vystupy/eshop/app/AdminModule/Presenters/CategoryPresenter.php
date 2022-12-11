<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\CategoryEditForm\CategoryEditForm;
use App\AdminModule\Components\CategoryEditForm\CategoryEditFormFactory;
use App\Model\Facades\CategoriesFacade;

/**
 * Class CategoryPresenter
 * @package App\AdminModule\Presenters
 */
class CategoryPresenter extends BasePresenter{
  private CategoriesFacade $categoriesFacade;
  private CategoryEditFormFactory $categoryEditFormFactory;

  /**
   * Akce pro vykreslení seznamu kategorií
   */
  public function renderDefault():void {
    $this->template->categories=$this->categoriesFacade->findCategories(['order'=>'title']);
  }

  /**
   * Akce pro úpravu jedné kategorie
   * @param int $id
   * @throws \Nette\Application\AbortException
   */
  public function renderEdit(int $id):void {
    try{
      $category=$this->categoriesFacade->getCategory($id);
    }catch (\Exception $e){
      $this->flashMessage('Požadovaná kategorie nebyla nalezena.', 'error');
      $this->redirect('default');
    }
    $form=$this->getComponent('categoryEditForm');
    $form->setDefaults($category);
    $this->template->category=$category;
  }

  /**
   * Akce pro smazání kategorie
   * @param int $id
   * @throws \Nette\Application\AbortException
   */
  public function actionDelete(int $id):void {
    try{
      $category=$this->categoriesFacade->getCategory($id);
    }catch (\Exception $e){
      $this->flashMessage('Požadovaná kategorie nebyla nalezena.', 'error');
      $this->redirect('default');
    }

    if (!$this->user->isAllowed($category,'delete')){
      $this->flashMessage('Tuto kategorii není možné smazat.', 'error');
      $this->redirect('default');
    }

    if ($this->categoriesFacade->deleteCategory($category)){
      $this->flashMessage('Kategorie byla smazána.', 'info');
    }else{
      $this->flashMessage('Tuto kategorii není možné smazat.', 'error');
    }

    $this->redirect('default');
  }

  /**
   * Formulář na editaci kategorií
   * @return CategoryEditForm
   */
  public function createComponentCategoryEditForm():CategoryEditForm {
    $form = $this->categoryEditFormFactory->create();
    $form->onCancel[]=function(){
      $this->redirect('default');
    };
    $form->onFinished[]=function($message=null){
      if (!empty($message)){
        $this->flashMessage($message);
      }
      $this->redirect('default');
    };
    $form->onFailed[]=function($message=null){
      if (!empty($message)){
        $this->flashMessage($message,'error');
      }
      $this->redirect('default');
    };
    return $form;
  }

  #region injections
  public function injectCategoriesFacade(CategoriesFacade $categoriesFacade):void {
    $this->categoriesFacade=$categoriesFacade;
  }
  public function injectCategoryEditFormFactory(CategoryEditFormFactory $categoryEditFormFactory):void {
    $this->categoryEditFormFactory=$categoryEditFormFactory;
  }
  #endregion injections

}
