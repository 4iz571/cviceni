<?php

namespace App\AdminModule\Components\ProductEditForm;

use App\Model\Entities\Product;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class ProductEditForm
 * @package App\AdminModule\Components\ProductEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class ProductEditForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public array $onFinished = [];
  /** @var callable[] $onFailed */
  public array $onFailed = [];
  /** @var callable[] $onCancel */
  public array $onCancel = [];

  private CategoriesFacade $categoriesFacade;
  private ProductsFacade $productsFacade;

  /**
   * ProductEditForm constructor.
   * @param CategoriesFacade $categoriesFacade
   * @param ProductsFacade $productsFacade
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   */
  public function __construct(CategoriesFacade $categoriesFacade, ProductsFacade $productsFacade, Nette\ComponentModel\IContainer $parent = null, string $name = null){
    parent::__construct($parent, $name);
    $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
    $this->categoriesFacade=$categoriesFacade;
    $this->productsFacade=$productsFacade;
    $this->createSubcomponents();
  }

  private function createSubcomponents():void {
    $productId=$this->addHidden('productId');
    $this->addText('title','Název produktu')
      ->setRequired('Musíte zadat název produktu')
      ->setMaxLength(100);

    $this->addText('url','URL produktu')
      ->setMaxLength(100)
      ->addFilter(function(string $url){
        return Nette\Utils\Strings::webalize($url);
      })
      ->addRule(function(Nette\Forms\Controls\TextInput $input)use($productId){
        try{
          $existingProduct = $this->productsFacade->getProductByUrl($input->value);
          return $existingProduct->productId==$productId->value;
        }catch (\Exception $e){
          return true;
        }
      },'Zvolená URL je již obsazena jiným produktem');

    #region kategorie
    $categories=$this->categoriesFacade->findCategories();
    $categoriesArr=[];
    foreach ($categories as $category){
      $categoriesArr[$category->categoryId]=$category->title;
    }
    $this->addSelect('categoryId','Kategorie',$categoriesArr)
      ->setPrompt('--vyberte kategorii--')
      ->setRequired(false);
    #endregion kategorie

    $this->addTextArea('description', 'Popis produktu')
      ->setRequired('Zadejte popis produktu.');

    $this->addText('price', 'Cena')
      ->setHtmlType('number')
      ->addRule(Form::NUMERIC,'Musíte zadat číslo.')
      ->setRequired('Musíte zadat cenu produktu');//tady by mohly být další kontroly pro min, max atp.

    $this->addCheckbox('available', 'Nabízeno ke koupi')
      ->setDefaultValue(true);

    #region obrázek
    $photoUpload=$this->addUpload('photo','Fotka produktu');
    //pokud není zadané ID produktu, je nahrání fotky povinné
    $photoUpload //vyžadování nahrání souboru, pokud není známé productId
      ->addConditionOn($productId, Form::EQUAL, '')
        ->setRequired('Pro uložení nového produktu je nutné nahrát jeho fotku.');

    $photoUpload //limit pro velikost nahrávaného souboru
      ->addRule(Form::MAX_FILE_SIZE, 'Nahraný soubor je příliš velký', 1000000);

    $photoUpload //kontrola typu nahraného souboru, pokud je nahraný
      ->addCondition(Form::FILLED)
        ->addRule(function(Nette\Forms\Controls\UploadControl $photoUpload){
          $uploadedFile = $photoUpload->value;
          if ($uploadedFile instanceof Nette\Http\FileUpload){
            $extension=strtolower($uploadedFile->getImageFileExtension());
            return in_array($extension,['jpg','jpeg','png']);
          }
          return false;
        },'Je nutné nahrát obrázek ve formátu JPEG či PNG.');
    #endregion obrázek

    $this->addSubmit('ok','uložit')
      ->onClick[]=function(SubmitButton $button){
        $values=$this->getValues('array');
        if (!empty($values['productId'])){
          try{
            $product=$this->productsFacade->getProduct($values['productId']);
          }catch (\Exception $e){
            $this->onFailed('Požadovaný produkt nebyl nalezen.');
            return;
          }
        }else{
          $product=new Product();
        }
        $product->assign($values,['title','url','description','available']);
        $product->price=floatval($values['price']);
        $this->productsFacade->saveProduct($product);
        $this->setValues(['productId'=>$product->productId]);

        //uložení fotky
        if (($values['photo'] instanceof Nette\Http\FileUpload) && ($values['photo']->isOk())){
          try{
            $this->productsFacade->saveProductPhoto($values['photo'], $product);
          }catch (\Exception $e){
            $this->onFailed('Produkt byl uložen, ale nepodařilo se uložit jeho fotku.');
          }
        }

        $this->onFinished('Produkt byl uložen.');
      };
    $this->addSubmit('storno','zrušit')
      ->setValidationScope([$productId])
      ->onClick[]=function(SubmitButton $button){
        $this->onCancel();
      };
  }

  /**
   * Metoda pro nastavení výchozích hodnot formuláře
   * @param Product|array|object $values
   * @param bool $erase = false
   * @return $this
   */
  public function setDefaults($values, bool $erase = false):self {
    if ($values instanceof Product){
      $values = [
        'productId'=>$values->productId,
        'categoryId'=>$values->category?$values->category->categoryId:null,
        'title'=>$values->title,
        'url'=>$values->url,
        'description'=>$values->description,
        'price'=>$values->price
      ];
    }
    parent::setDefaults($values, $erase);
    return $this;
  }

}