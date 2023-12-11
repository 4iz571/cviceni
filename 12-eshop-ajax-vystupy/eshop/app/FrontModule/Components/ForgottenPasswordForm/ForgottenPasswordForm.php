<?php

namespace App\FrontModule\Components\ForgottenPasswordForm;

use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class ForgottenPasswordForm
 * @package App\FrontModule\Components\ForgottenPasswordForm
 *
 * @method onFinished($message='')
 * @method onCancel()
 */
class ForgottenPasswordForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public array $onFinished = [];
  /** @var callable[] $onCancel */
  public array $onCancel = [];

  private UsersFacade $usersFacade;
  private Nette\Application\LinkGenerator $linkGenerator;
  private string $mailFromEmail = '';
  private string $mailFromName = '';

  /**
   * ForgottenPasswordForm constructor.
   * @param UsersFacade $usersFacade
   * @param Nette\Application\LinkGenerator $linkGenerator
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   */
  public function __construct(UsersFacade $usersFacade, Nette\Application\LinkGenerator $linkGenerator, ?Nette\ComponentModel\IContainer $parent = null, ?string $name = null){
    parent::__construct($parent, $name);
    $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
    $this->usersFacade=$usersFacade;
    $this->createSubcomponents();
    $this->linkGenerator=$linkGenerator;
  }

  /**
   * Metoda volaná z configu, která se používá pro nastavení údajů o odesílateli e-mailů
   * @param string $email
   * @param string $name
   */
  public function setMailFrom(string $email, string $name):void {
    $this->mailFromEmail=$email;
    $this->mailFromName=$name;
  }

  private function createSubcomponents():void {
    $this->addEmail('email','E-mail')
      ->setRequired('Zadejte platný email');

    $this->addSubmit('ok','poslat e-mail pro obnovu hesla')
      ->onClick[]=function(SubmitButton $button){
        //získáme z formuláře zadaný e-mail
        $userEmail = $this->values->email;

        //najdeme daného uživatele
        try{
          $user = $this->usersFacade->getUserByEmail($userEmail);
        }catch (\Exception $e){
          //uživatel nebyl nalezen - zvažte, zda o tom informovat uživatele, či nikoliv
          $this->onFinished('Pokud uživatelský účet s daným e-mailem existuje, poslali jsme vám odkaz na změnu hesla.');
          return;
        }

        //vygenerování odkaz na změnu hesla
        $forgottenPassword = $this->usersFacade->saveNewForgottenPasswordCode($user);
        $mailLink = $this->linkGenerator->link('//Front:User:renewPassword', ['user'=>$user->userId, 'code'=>$forgottenPassword->code]);

        #region příprava textu mailu
        $mail = new Nette\Mail\Message();
        $mail->setFrom($this->mailFromEmail, $this->mailFromName);
        $mail->addTo($user->email, $user->name);
        $mail->subject = 'Obnova zapomenutého hesla';
        $mail->htmlBody = 'Obdrželi jsme vaši žádost na obnovu zapomenutého hesla. Pokud si přejete heslo změnit, <a href="'.$mailLink.'">klikněte zde</a>.';
        #endregion endregion příprava textu mailu

        //odeslání mailu pomocí PHP funkce mail
        $mailer = new Nette\Mail\SendmailMailer;
        $mailer->send($mail);

        $this->onFinished();
      };
    $this->addSubmit('storno','zrušit')
      ->setValidationScope([])
      ->onClick[]=function(SubmitButton $button){
        $this->onCancel();
      };
  }

}