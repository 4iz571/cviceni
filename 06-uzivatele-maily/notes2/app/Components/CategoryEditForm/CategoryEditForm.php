<?php

namespace App\Components\CategoryEditForm;

use App\Model\Entities\Category;
use App\Model\Facades\CategoriesFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

/**
 * Class CategoryEditForm
 * @package App\Components\CategoryEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class CategoryEditForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public array $onFinished = [];
  /** @var callable[] $onFailed */
  public array $onFailed = [];
  /** @var callable[] $onCancel */
  public array $onCancel = [];

  private CategoriesFacade $categoriesFacade;

  /**
   * TagEditForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param CategoriesFacade $categoriesFacade
   */
  public function __construct(CategoriesFacade $categoriesFacade, Nette\ComponentModel\IContainer $parent = null, string $name = null){
    parent::__construct($parent, $name);
    $this->categoriesFacade=$categoriesFacade;
    $this->createSubcomponents();
  }

  private function createSubcomponents():void {
    $categoryId=$this->addHidden('categoryId');
    $this->addText('title','Název kategorie')
      ->setRequired('Musíte zadat název kategorie');
    $this->addTextArea('description','Popis kategorie')
      ->setRequired(false);
    $this->addSubmit('ok','uložit')
      ->setHtmlAttribute('class','btn btn-light')
      ->onClick[]=function(SubmitButton $button){
        $values=$this->getValues('array');
        if (!empty($values['categoryId'])){
          try{
            $category=$this->categoriesFacade->getCategory($values['categoryId']);
          }catch (\Exception $e){
            $this->onFailed('Požadovaná kategorie nebyla nalezena.');
            return;
          }
        }else{
          $category=new Category();
        }
        $category->assign($values,['title','description']);
        $this->categoriesFacade->saveCategory($category);
        $this->setValues(['categoryId'=>$category->categoryId]);
        $this->onFinished('Kategorie byla uložena.');
      };
    $this->addSubmit('storno','zrušit')
      ->setHtmlAttribute('class','btn btn-light')
      ->setValidationScope([$categoryId])
      ->onClick[]=function(SubmitButton $button){
        $this->onCancel();
      };
  }

  /**
   * Metoda pro nastavení výchozích hodnot formuláře
   * @param Category|array|object $values
   * @param bool $erase = false
   * @return $this
   */
  public function setDefaults($values, bool $erase = false):self {
    if ($values instanceof Category){
      $values = [
        'categoryId'=>$values->categoryId,
        'title'=>$values->title,
        'description'=>$values->description
      ];
    }
    parent::setDefaults($values, $erase);
    return $this;
  }

}