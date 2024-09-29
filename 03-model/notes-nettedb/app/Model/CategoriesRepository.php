<?php

namespace App\Model;

use Nette\Database\Row;
use Nette\Database\Connection;

/**
 * Class CategoriesRepository
 * @package App\Model
 */
class CategoriesRepository{

  private Connection $database;

  /**
   * CategoriesRepository constructor.
   * @param Connection $database
   */
  public function __construct(Connection $database){
    $this->database=$database;
  }

  /**
   * Metoda pro načtení jedné kategorie
   * @param int $id
   * @return Row
   * @throws \Exception
   */
  public function getCategory(int $id):Row {
    if ($category=$this->database->fetch('SELECT * FROM category WHERE category_id=?', $id)){
      return $category;
    }else{
      throw new \Exception('Category not found.');
    }
  }

  /**
   * Metoda pro vyhledání kategorií
   * @param array|null $params = null
   * @param int $offset = 0
   * @param int $limit = 1000
   * @return array
   */
  public function findCategories(?array $params=null,int $offset=0,int $limit=1000):array {
    if (isset($params['order'])){
      $order=$params['order'];
      unset($params['order']);
    }
    if (empty($order)){
      $order='title';
    }
    if (!empty($params)){
      return $this->database->fetchAll('SELECT * FROM category WHERE ',$params,' ORDER BY ?order LIMIT ? OFFSET ?',[$order=>true],$limit,$offset);
    }else{
      return $this->database->fetchAll('SELECT * FROM category ORDER BY ?order LIMIT ? OFFSET ?',[$order=>true],$limit,$offset);
    }
  }

  /**
   * Metoda pro zjištění počtu kategorií
   * @param array|null $params
   * @return int
   */
  public function findCategoriesCount(?array $params=null):int {
    if (!empty($params)){
      return $this->database->fetchField('SELECT count(*) FROM category WHERE ',$params);
    }else{
      return $this->database->fetchField('SELECT count(*) FROM category');
    }
  }

  /**
   * Metoda pro uložení kategorie
   * @param array $categoryData
   * @return int - ID vloženého záznamu
   */
  public function saveNewCategory(array $categoryData):int {
    $this->database->query('INSERT INTO category ',[
      'title'=>@$categoryData['title'],
      'description'=>@$categoryData['description'],
    ]);
    return $this->database->getInsertId();
  }


}