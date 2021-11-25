<?php

namespace App\AdminModule\Components\CategoryEditForm;

use App\Model\Entities\Category;
use App\Model\Facades\CategoriesFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class CategoryEditForm
 * @package App\AdminModule\Components\CategoryEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class CategoryEditForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onFailed */
  public $onFailed = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];
  /** @var CategoriesFacade $tagsFacade */
  private $categoriesFacade;

  /**
   * TagEditForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param CategoriesFacade $categoriesFacade
   * @noinspection PhpOptionalBeforeRequiredParametersInspection
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, CategoriesFacade $categoriesFacade){
    parent::__construct($parent, $name);
    $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
    $this->categoriesFacade=$categoriesFacade;
    $this->createSubcomponents();
  }

  private function createSubcomponents(){
    $categoryId=$this->addHidden('categoryId');
    $this->addText('title','Název kategorie')
      ->setRequired('Musíte zadat název kategorie');
    $this->addTextArea('description','Popis kategorie')
      ->setRequired(false);
    $this->addSubmit('ok','uložit')
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