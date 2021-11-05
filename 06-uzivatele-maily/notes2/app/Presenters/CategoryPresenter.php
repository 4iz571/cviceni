<?php

declare(strict_types=1);
namespace App\Presenters;

use App\Components\CategoryEditForm\CategoryEditForm;
use App\Components\CategoryEditForm\CategoryEditFormFactory;
use App\Model\Facades\CategoriesFacade;

/**
 * Class CategoryPresenter
 * @package App\Presenters
 */
class CategoryPresenter extends \Nette\Application\UI\Presenter {
  /** @var CategoriesFacade $categoriesFacade */
  private /*CategoriesFacade*/ $categoriesFacade;
  /** @var CategoryEditFormFactory $categoryEditFormFactory */
  private /*CategoryEditFormFactory*/ $categoryEditFormFactory;

  /**
   * Výchozí akce - zatím jen přesměrovává na seznam kategorií
   * @throws \Nette\Application\AbortException
   */
  public function actionDefault(){
    $this->redirect('list');
  }

  /**
   * Akce pro zobrazení seznamu dostupných kategorií
   */
  public function renderList(){
    $this->template->categories=$this->categoriesFacade->findCategories(['order'=>'title']);
  }

  /**
   * Akce pro zobrazení detailů jedné kategorie
   * @param int $id
   * @throws \Nette\Application\BadRequestException
   */
  public function renderShow(int $id):void {
    try{
      $this->template->category=$this->categoriesFacade->getCategory($id);
    }catch (\Exception $e){
      $this->error('Požadovaná kategorie nebyla nalezena', 404);
    }
  }
  /**
   * Akce pro úpravu jedné kategorie
   * @param int $id
   * @throws \Nette\Application\BadRequestException
   */
  public function renderEdit(int $id):void {
    try{
      $category=$this->categoriesFacade->getCategory($id);
    }catch (\Exception $e){
      $this->error('Požadovaná kategorie nebyla nalezena', 404);
    }
    $form=$this->getComponent('categoryEditForm');
    $form->setDefaults($category);
    $this->template->category=$category;
  }

  /**
   * Formulář na editaci kategorií
   * @return CategoryEditForm
   */
  public function createComponentCategoryEditForm():CategoryEditForm {
    $form = $this->categoryEditFormFactory->create();
    $form->onCancel[]=function(){
      $this->redirect('list');
    };
    $form->onFinished[]=function($message=null){
      if (!empty($message)){
        $this->flashMessage($message);
      }
      $this->redirect('list');
    };
    $form->onFailed[]=function($message=null){
      if (!empty($message)){
        $this->flashMessage($message,'error');
      }
      $this->redirect('list');
    };
    return $form;
  }

  #region injections
  public function injectCategoriesFacade(CategoriesFacade $categoriesFacade){
    $this->categoriesFacade=$categoriesFacade;
  }
  public function injectCategoryEditFormFactory(CategoryEditFormFactory $categoryEditFormFactory){
    $this->categoryEditFormFactory=$categoryEditFormFactory;
  }
  #endregion injections
}
