<?php

namespace App\Model\Facades;

use App\Model\Entities\Category;
use App\Model\Repositories\CategoryRepository;

/**
 * Class CategoriesFacade - fasáda pro využívání kategorií z presenterů
 * @package App\Model\Facades
 */
class CategoriesFacade{
  private CategoryRepository $categoryRepository;

  public function __construct(CategoryRepository $categoryRepository){
    $this->categoryRepository=$categoryRepository;
  }

  /**
   * Metoda pro načtení jedné kategorie
   * @param int $id
   * @return Category
   * @throws \Exception
   */
  public function getCategory(int $id):Category {
    return $this->categoryRepository->find($id); //buď počítáme s možností vyhození výjimky, nebo ji ošetříme už tady a můžeme vracet např. null
  }

  /**
   * Metoda pro vyhledání kategorií
   * @param array|null $params = null
   * @param int $offset = null
   * @param int $limit = null
   * @return Category[]
   */
  public function findCategories(?array $params=null,?int $offset=null,?int $limit=null):array {
    return $this->categoryRepository->findAllBy($params,$offset,$limit);
  }

  /**
   * Metoda pro zjištění počtu kategorií
   * @param array|null $params
   * @return int
   */
  public function findCategoriesCount(?array $params=null):int {
    return $this->categoryRepository->findCountBy($params);
  }

  /**
   * Metoda pro uložení kategorie
   * @param Category &$category
   * @return bool - true, pokud byly v DB provedeny nějaké změny
   */
  public function saveCategory(Category &$category):bool {
    return (bool)$this->categoryRepository->persist($category);
  }

  /**
   * Metoda pro smazání kategorie
   * @param Category $category
   * @return bool
   */
  public function deleteCategory(Category $category):bool {
    try{
      return (bool)$this->categoryRepository->delete($category);
    }catch (\Exception $e){
      return false;
    }
  }

}