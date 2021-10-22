<?php

namespace App\Model\Facades;

use App\Model\Entities\Tag;
use App\Model\Repositories\TagRepository;
use LeanMapper\Exception\InvalidStateException;

/**
 * Class TagsFacade - fasáda pro přístup k jednotlivým tagům
 * @package App\Model\Facades
 */
class TagsFacade{
  /** @var TagRepository $tagRepository */
  private $tagRepository;

  public function __construct(TagRepository $tagRepository){
    $this->tagRepository=$tagRepository;
  }

  /**
   * Metoda pro jednoduché nalezení či vytvoření tagu podle jeho názvu
   * @param string $title
   * @return Tag
   */
  public function getOrCreateTagByTitle(string $title):Tag {
    try{
      return $this->tagRepository->findBy(['title'=>$title]);
    }catch (\Exception $e){
      //nepovedlo se tag najít, vytvoříme nový
      $tag = new Tag();
      $tag->title=$title;
      $this->saveTag($tag);
      return $tag;
    }
  }

  /**
   * Metoda pro načtení jednoho úkolu
   * @param int $id
   * @return Tag
   * @throws \Exception
   */
  public function getTag(int $id):Tag {
    return $this->tagRepository->find($id);
  }

  /**
   * Metoda pro vyhledání tagů
   * @param int $offset = null
   * @param int $limit = null
   * @return Tag[]
   */
  public function findTags(int $offset=null,int $limit=null):array {
    return $this->tagRepository->findAllBy(['order'=>'title'],$offset,$limit);
  }

  /**
   * Metoda pro zjištění počtu tagů
   * @param array|null $params
   * @return int
   */
  public function findTodosCount():int {
    return $this->tagRepository->findCountBy();
  }

  /**
   * Metoda pro uložení tagu
   * @param Tag &$tag
   * @return bool
   */
  public function saveTag(Tag &$tag):bool {
    return (bool)$this->tagRepository->persist($tag);
  }

  /**
   * Metoda pro smazání tagu
   * @param Tag $tag
   * @return bool
   */
  public function deleteTag(Tag $tag):bool {
    try{
      return (bool)$this->tagRepository->delete($tag);
    }catch (InvalidStateException $e){
      return false;
    }
  }

}