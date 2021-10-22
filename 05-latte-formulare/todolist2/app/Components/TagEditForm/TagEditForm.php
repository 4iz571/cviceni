<?php

namespace App\Components\TagEditForm;

use App\Model\Entities\Tag;
use App\Model\Facades\TagsFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

/**
 * Class TagEditForm
 * @package App\Components\TagEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class TagEditForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onFailed */
  public $onFailed = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];
  /** @var TagsFacade $tagsFacade */
  private $tagsFacade;

  /**
   * TagEditForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param TagsFacade $tagsFacade
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, TagsFacade $tagsFacade){
    parent::__construct($parent, $name);
    $this->tagsFacade = $tagsFacade;
    $this->createSubcomponents();
  }

  /**
   * Metoda vytvářející vnitřní strukturu formuláře
   */
  private function createSubcomponents():void {
    $this->addProtection('Opakujte prosím odeslání formuláře znovu.');
    $tagId=$this->addHidden('tagId');
    $this->addText('title')
      ->setRequired('Zadejte název tagu!');
    $this->addSubmit('save', 'uložit')
      ->onClick[]=function(SubmitButton $submitButton){
        #region akce pro save
        $values = $this->getValues('array');
        if (!empty($values['tagId'])){
          //chceme najít existující tag podle jeho ID
          try{
            $tag = $this->tagsFacade->getTag($values['tagId']);
          }catch (\Exception $e){
            $this->onFailed('Tag nebyl nalezen.');
            return;
          }
        }else{
          //chceme vytvořit nový tag
          $tag = new Tag();
        }
        $tag->title=$values['title'];
        $this->tagsFacade->saveTag($tag);
        $this->setValues(['tagId'=>$tag->tagId]);
        $this->onFinished('Tag byl uložen.');
        #endregion akce pro save
      };
    $this->addSubmit('cancel','zrušit')
      ->setValidationScope([$tagId])
      ->onClick[]=function(){
        #region akce pro cancel
        $this->onCancel();
        #endregion akce pro cancel
      };
  }

  /**
   * Metoda pro nastavení výchozích hodnot formuláře
   * @param Tag|array|object $values
   * @param bool $erase = false
   * @return $this
   */
  public function setDefaults($values, bool $erase = false):self {
    if ($values instanceof Tag){
      $values = [
        'tagId'=>$values->tagId,
        'title'=>$values->title
      ];
    }
    parent::setDefaults($values, $erase);
    return $this;
  }

}