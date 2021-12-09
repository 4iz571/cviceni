<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\ProductCartForm\ProductCartFormFactory;
use App\Model\Facades\ProductsFacade;
use Nette\Application\BadRequestException;

/**
 * Class ProductPresenter
 * @package App\FrontModule\Presenters
 * @property string $category
 */
class ProductPresenter extends BasePresenter{
  /** @var ProductsFacade $productsFacade */
  private $productsFacade;
  /** @var ProductCartFormFactory $productCartFormFactory */
  private $productCartFormFactory;

  /** @persistent */
  public $category;

  /**
   * Akce pro zobrazení jednoho produktu
   * @param string $url
   * @throws BadRequestException
   */
  public function renderShow(string $url):void {
    try{
      $product = $this->productsFacade->getProductByUrl($url);
    }catch (\Exception $e){
      throw new BadRequestException('Produkt nebyl nalezen.');
    }

    $this->template->product = $product;
  }

  /**
   * Akce pro vykreslení přehledu produktů
   */
  public function renderList():void {
    //TODO tady by mělo přibýt filtrování podle kategorie, stránkování atp.
    $this->template->products = $this->productsFacade->findProducts(['order'=>'title']);
  }

  #region injections
  public function injectProductsFacade(ProductsFacade $productsFacade):void {
    $this->productsFacade=$productsFacade;
  }

  public function injectProductCartFormFactory(ProductCartFormFactory $productCartFormFactory):void {
    $this->productCartFormFactory=$productCartFormFactory;
  }
  #endregion injections
}