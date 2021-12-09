<?php

namespace App\FrontModule\Components\ProductCartForm;

use App\FrontModule\Components\CartControl\CartControl;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class ProductCartForm
 * @package App\FrontModule\Components\ProductCartForm
 *
 * @method onFinished()
 */
class ProductCartForm extends Form{

  use SmartObject;

  /** @var CartControl $cartControl */
  private $cartControl;

  /**
   * ProductCartForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null){
    parent::__construct($parent, $name);
    $this->setRenderer(new Bs4FormRenderer(FormLayout::HORIZONTAL));
    $this->createSubcomponents();
  }

  /**
   * Metoda pro předání komponenty košíku jako závislosti
   * @param CartControl $cartControl
   */
  public function setCartControl(CartControl $cartControl):void {
    $this->cartControl=$cartControl;
  }

  private function createSubcomponents(){
    $this->addHidden('productId');
    $this->addInteger('count','Počet kusů')
      ->addRule(Form::RANGE,'Chybný počet kusů.',[1,100]);

    $this->addSubmit('ok','přidat do košíku')
      ->onClick[]=function(SubmitButton $button){
        //přidání zboží do košíku
        //TODO
      };
  }

}