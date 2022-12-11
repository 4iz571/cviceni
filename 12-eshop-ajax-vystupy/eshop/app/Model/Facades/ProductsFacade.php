<?php

namespace App\Model\Facades;

use App\Model\Entities\Product;
use App\Model\Repositories\ProductRepository;
use Nette\Http\FileUpload;
use Nette\Utils\Strings;

/**
 * Class ProductsFacade
 * @package App\Model\Facades
 */
class ProductsFacade{
  private ProductRepository $productRepository;

  /**
   * Metoda pro získání jednoho produktu
   * @param int $id
   * @return Product
   * @throws \Exception
   */
  public function getProduct(int $id):Product {
    return $this->productRepository->find($id);
  }

  /**
   * Metoda pro získání produktu podle URL
   * @param string $url
   * @return Product
   * @throws \Exception
   */
  public function getProductByUrl(string $url):Product {
    return $this->productRepository->findBy(['url'=>$url]);
  }

  /**
   * Metoda pro vyhledání produktů
   * @param array|null $params = null
   * @param int $offset = null
   * @param int $limit = null
   * @return Product[]
   */
  public function findProducts(array $params=null,int $offset=null,int $limit=null):array {
    return $this->productRepository->findAllBy($params,$offset,$limit);
  }

  /**
   * Metoda pro zjištění počtu produktů
   * @param array|null $params
   * @return int
   */
  public function findProductsCount(array $params=null):int {
    return $this->productRepository->findCountBy($params);
  }

  /**
   * Metoda pro uložení produktu
   * @param Product &$product
   */
  public function saveProduct(Product &$product):void {
    #region URL produktu
    if (empty($product->url)){
      //pokud je URL prázdná, vygenerujeme ji podle názvu produktu
      $baseUrl=Strings::webalize($product->title);
    }else{
      $baseUrl=$product->url;
    }

    #region vyhledání produktů se shodnou URL (v případě shody připojujeme na konec URL číslo)
    $urlNumber=1;
    $url=$baseUrl;
    $productId = isset($product->productId)?$product->productId:null;
    try{
      while ($existingProduct = $this->getProductByUrl($url)){
        if ($existingProduct->productId==$productId){
          //ID produktu se shoduje => je v pořádku, že je URL stejná
          $product->url=$url;
          break;
        }
        $urlNumber++;
        $url=$baseUrl.$urlNumber;
      }
    }catch (\Exception $e){
      //produkt nebyl nalezen => URL je použitelná
    }
    $product->url=$url;
    #endregion vyhledání produktů se shodnou URL (v případě shody připojujeme na konec URL číslo)
    #endregion URL produktu

    $this->productRepository->persist($product);
  }

  /**
   * Metoda pro uložení fotky produktu
   * @param FileUpload $fileUpload
   * @param Product $product
   * @throws \Exception
   */
  public function saveProductPhoto(FileUpload $fileUpload, Product &$product):void {
    if ($fileUpload->isOk() && $fileUpload->isImage()){
      $fileExtension=strtolower($fileUpload->getImageFileExtension());
      $fileUpload->move(__DIR__.'/../../../www/img/products/'.$product->productId.'.'.$fileExtension);
      $product->photoExtension=$fileExtension;
      $this->saveProduct($product);
    }
  }


  public function __construct(ProductRepository $productRepository){
    $this->productRepository=$productRepository;
  }
}