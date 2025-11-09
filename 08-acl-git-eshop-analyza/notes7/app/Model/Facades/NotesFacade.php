<?php

namespace App\Model\Facades;

use App\Model\Entities\Category;
use App\Model\Entities\Note;
use App\Model\Repositories\NoteRepository;

/**
 * Class NotesFacade
 * @package App\Model\Facades
 */
class NotesFacade{
  private NoteRepository $noteRepository;

  public function __construct(NoteRepository $noteRepository){
    $this->noteRepository=$noteRepository;
  }

  /**
   * Metoda pro načtení jednoho příspěvku
   * @param int $id
   * @return Note
   * @throws \Exception
   */
  public function getNote(int $id):Note {
    /** @noinspection PhpIncompatibleReturnTypeInspection */
    return $this->noteRepository->find($id); //buď počítáme s možností vyhození výjimky, nebo ji ošetříme už tady a můžeme vracet např. null
  }

  /**
   * Metoda pro vyhledání poznámek
   * @param null $category
   * @param int|null $offset = null
   * @param int|null $limit = null
   * @return Note[]
   */
  public function findNotes($category = null, ?int $offset=null, ?int $limit=null):array {
    if ($category){
      $searchParams=['category_id'=>($category instanceof Category?$category->categoryId:$category)];
    }else{
      $searchParams=[];
    }
    return $this->noteRepository->findAllBy($searchParams, $offset, $limit);
  }

  /**
   * Metoda pro zjištění počtu poznámek
   * @param array|null $category
   * @return int
   */
  public function findNotesCount($category = null):int {
    if ($category){
      $searchParams=['category_id'=>($category instanceof Category?$category->categoryId:$category)];
    }else{
      $searchParams=[];
    }
    return $this->noteRepository->findCountBy($searchParams);
  }

  /**
   * Metoda pro uložení poznámky
   * @param Note &$note
   * @return bool
   */
  public function saveNote(Note &$note):bool {
    return (bool)$this->noteRepository->persist($note);
  }

  /**
   * Metoda pro smazání poznámky
   * @param Note $note
   * @return bool
   * @throws \LeanMapper\Exception\InvalidStateException
   */
  public function deleteNote(Note $note):bool {
    return (bool)$this->noteRepository->delete($note);
  }

}