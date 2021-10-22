<?php

namespace App\Model\Facades;

use App\Model\Entities\Todo;
use App\Model\Entities\TodoItem;
use App\Model\Repositories\TodoItemRepository;
use App\Model\Repositories\TodoRepository;

/**
 * Class TodosFacade - fasáda pro přístup k jednotlivým úkolům
 * @package App\Model\Facades
 */
class TodosFacade{
  /** @var TodoRepository $todoRepository */
  private $todoRepository;
  /** @var TodoItemRepository $todoItemRepository */
  private $todoItemRepository;

  public function __construct(TodoRepository $todoRepository, TodoItemRepository $todoItemRepository){
    $this->todoRepository=$todoRepository;
    $this->todoItemRepository=$todoItemRepository;
  }

  /**
   * Metoda pro načtení jednoho úkolu
   * @param int $id
   * @return Todo
   * @throws \Exception
   */
  public function getTodo(int $id):Todo {
    return $this->todoRepository->find($id);
  }

  /**
   * Metoda pro vyhledání úkolů
   * @param array|null $params = null
   * @param int $offset = null
   * @param int $limit = null
   * @return Todo[]
   */
  public function findTodos(array $params=null,int $offset=null,int $limit=null):array {
    return $this->todoRepository->findAllBy($params,$offset,$limit);
  }

  /**
   * Metoda pro zjištění počtu úkolů
   * @param array|null $params
   * @return int
   */
  public function findTodosCount(array $params=null):int {
    return $this->todoRepository->findCountBy($params);
  }

  /**
   * Metoda pro uložení úkolu
   * @param Todo &$todo
   * @return bool
   */
  public function saveTodo(Todo &$todo):bool {
    return (bool)$this->todoRepository->persist($todo);
  }

  /**
   * Metoda pro uložení položky úkolu
   * @param TodoItem $todoItem
   * @return bool
   */
  public function saveTodoItem(TodoItem &$todoItem):bool {
    return (bool)$this->todoItemRepository->persist($todoItem);
  }


}