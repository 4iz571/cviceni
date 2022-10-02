<?php

declare(strict_types=1);
namespace App\Presenters;

use App\Model\CategoriesRepository;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

class CategoryPresenter extends \Nette\Application\UI\Presenter {
  private CategoriesRepository $categoriesRepository;

  /**
   * @throws \Nette\Application\AbortException
   */
  public function actionDefault():void {
    $this->redirect('list');
  }

  /**
   * Akce pro zobrazení seznamu dostupných kategorií
   */
  public function renderList():void {
    $this->template->categories=$this->categoriesRepository->findCategories(['order'=>'title']);
  }

  /**
   * Akce pro zobrazení detailů jedné kategorie
   * @param int $id
   * @throws \Nette\Application\BadRequestException
   */
  public function renderShow(int $id):void {
    try{
      $this->template->category=$this->categoriesRepository->getCategory($id);
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
      $category=$this->categoriesRepository->getCategory($id);
    }catch (\Exception $e){
      $this->error('Požadovaná kategorie nebyla nalezena', 404);
    }
    $form=$this->getComponent('categoryEditForm');
    $form->setDefaults([
      'category_id'=>$category->category_id,
      'title'=>$category->title,
      'description'=>$category->description
    ]);
    $this->template->category=$category;
  }

  /**
   * Formulář na editaci kategorií
   * @return Form
   */
  public function createComponentCategoryEditForm():Form {
    $form = new Form();
    $form->addHidden('category_id');
    $form->addText('title','Název kategorie')
      ->setRequired('Musíte zadat název kategorie');
    $form->addTextArea('description','Popis kategorie')
      ->setRequired(false);
    $form->addSubmit('save','uložit')
      ->setHtmlAttribute('class','btn btn-primary')
      ->onClick[]=function(SubmitButton $button){
        //hodnoty z formuláře získáme v podobě pole
        /** @var array $values */
        $values=$button->form->getValues(true);

        //provedení potřebné akce
        if (!empty($values['category_id'])){
          //TODO načtení kategorie z databáze a její aktualizace
        }else{
          //TODO vytvoření nové kategorie
        }

        //přesměrování na seznam kategorií
        $this->redirect('list');
      };
    $form->addSubmit('storno','zrušit')
      ->setHtmlAttribute('class','btn btn-light')
      ->setValidationScope([])
      ->onClick[]=function(SubmitButton $button){
        $this->redirect('list');
      };
    return $form;
  }







  public function injectCategoriesRepository(CategoriesRepository $categoriesRepository){
    $this->categoriesRepository=$categoriesRepository;
  }
}
