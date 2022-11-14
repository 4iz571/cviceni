<?php

namespace App\Components\NoteEditForm;

use App\Model\Entities\Category;
use App\Model\Entities\Note;
use App\Model\Entities\User;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\NotesFacade;
use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

/**
 * Class NoteEditForm
 * @package App\Components\NoteEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class NoteEditForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onFailed */
  public $onFailed = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];

  private CategoriesFacade $categoriesFacade;
  private NotesFacade $notesFacade;
  private UsersFacade $usersFacade;
  private User $user;

  /**
   * TagEditForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param CategoriesFacade $categoriesFacade
   * @noinspection PhpOptionalBeforeRequiredParametersInspection
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, Nette\Security\User $currentUser, CategoriesFacade $categoriesFacade, NotesFacade $notesFacade, UsersFacade $usersFacade){
    parent::__construct($parent, $name);
    $this->categoriesFacade=$categoriesFacade;
    $this->notesFacade=$notesFacade;
    $this->usersFacade=$usersFacade;

    if ($currentUser->isLoggedIn()){
      try{
        $this->user=$this->usersFacade->getUser($currentUser->id);
      }catch (\Exception $e){/*chybu přeskočíme - hned za načtením je zařazena kontrola přihlášeného uživatele*/}
    }
    if (!$this->user){
      //uživatel není přihlášen, nebo již není v DB
      $this->onFailed('Poznámky mohou vytvářet a editovat jen přihlášení uživatelé!');
      return;
    }

    $this->createSubcomponents();
  }

  /**
   * Vytvoření struktury formuláře
   */
  private function createSubcomponents():void{
    $noteId=$this->addHidden('noteId');
    $this->addText('title','Předmět:')
      ->setRequired('Musíte zadat předmět');
    $this->addTextArea('text','Text:')
      ->setRequired(false);
    $categoryItemsArr=[];
    $categories = $this->categoriesFacade->findCategories();
    if (!empty($categories)){
      foreach ($categories as $category){
        $categoryItemsArr[$category->categoryId]=$category->title;
      }
    }
    $this->addSelect('categoryId','Kategorie:',$categoryItemsArr)
      ->setPrompt('--vyberte--')
      ->setRequired('Vyberte kategorii, do které zadávaná poznámka patří.');

    $this->addSubmit('ok','uložit')
      ->setHtmlAttribute('class','btn btn-light')
      ->onClick[]=function(SubmitButton $button){
        $values=$this->getValues('array');
        if (!empty($values['noteId'])){
          try{
            $note=$this->notesFacade->getNote($values['categoryId']);
          }catch (\Exception $e){
            $this->onFailed('Požadovaná poznámka nebyla nalezena.');
            return;
          }
        }else{
          $note=new Note();
        }

        try{
          $category=$this->categoriesFacade->getCategory($values['categoryId']);
        }catch (\Exception $e){
          $this->onFailed('Požadovaná kategorie nebyla nalezena.');
          return;
        }

        $note->title=$values['title'];
        $note->text=$values['text'];
        $note->category=$category;
        $note->author=$this->user;

        $this->notesFacade->saveNote($note);

        $this->setValues(['noteId'=>$note->noteId]);

        $this->onFinished();
      };
    $this->addSubmit('storno','zrušit')
      ->setHtmlAttribute('class','btn btn-light')
      ->setValidationScope([$noteId])
      ->onClick[]=function(SubmitButton $button){
        $this->onCancel();
      };
  }

  /**
   * Metoda pro nastavení výchozích hodnot formuláře
   * @param Note|array|object $values
   * @param bool $erase = false
   * @return $this
   */
  public function setDefaults($values, bool $erase = false):self {
    if ($values instanceof Note){
      $values = [
        'noteId'=>$values->noteId,
        'title'=>$values->title,
        'text'=>$values->text,
        'categoryId'=>$values->category->categoryId
      ];
    }
    parent::setDefaults($values, $erase);
    return $this;
  }

}