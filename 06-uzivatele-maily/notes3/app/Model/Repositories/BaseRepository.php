<?php

namespace App\Model\Repositories;

use LeanMapper\Entity;
use LeanMapper\Exception\InvalidStateException;

abstract class BaseRepository extends \LeanMapper\Repository {
  /**
   * @param int $id
   * @return Entity
   * @throws \Exception
   */
  public function find(int $id):Entity {
    $row = $this->connection->select('*')
      ->from($this->getTable())
      ->where($this->mapper->getPrimaryKey($this->getTable()) . '= %i', $id)
      ->fetch();

    if (!$row) {
      throw new \Exception('Entity was not found.');
    }
    return $this->createEntity($row);
  }

  /**
   * @return Entity[]
   */
  public function findAll():array {
    return $this->createEntities(
      $this->connection->select('*')
        ->from($this->getTable())
        ->fetchAll()
    );
  }

  /**
   * @param null|array $whereArr = null
   * @return Entity
   * @throws \Exception
   */
  public function findBy(?array $whereArr = null):Entity {
    $query = $this->connection->select('*')->from($this->getTable());
    if ($whereArr != null) {
      $query = $query->where($whereArr);
    }
    $row = $query->fetch();
    if (!$row) {
      throw new \Exception('Entity was not found.');
    }
    return $this->createEntity($row);
  }

  /**
   * @param null|array $whereArr
   * @param null|int $offset
   * @param null|int $limit
   * @return Entity[]
   */
  public function findAllBy(?array $whereArr = null, ?int $offset = null, ?int $limit = null):array {
    $query = $this->connection->select('*')->from($this->getTable());
    if (isset($whereArr['order'])) {
      $query->orderBy($whereArr['order']);
      unset($whereArr['order']);
    }
    if ($whereArr != null && count($whereArr) > 0) {
      $query = $query->where($whereArr);
    }
    return $this->createEntities($query->fetchAll($offset, $limit));
  }

  /**
   * @param array|null $whereArr
   * @return int
   * @throws InvalidStateException
   */
  public function findCountBy(?array $whereArr = null):int {
    $query = $this->connection->select('count(*) as pocet')->from($this->getTable());
    if ($whereArr != null) {
      $query = $query->where($whereArr);
    }
    return $query->fetchSingle();
  }

}


