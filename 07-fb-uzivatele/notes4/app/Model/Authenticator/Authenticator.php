<?php

namespace App\Model\Authenticator;

use App\Model\Facades\UsersFacade;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

/**
 * Class Authenticator - jednoduchý autentifikátor ověřující uživatele vůči databázi
 * @package App\Model\Authenticator
 */
class Authenticator implements \Nette\Security\Authenticator{
  private UsersFacade $usersFacade;
  private Passwords $passwords;

  public function __construct(Passwords $passwords, UsersFacade $usersFacade){
    $this->passwords=$passwords;
    $this->usersFacade=$usersFacade;
  }

  /**
   * @inheritDoc
   */
  function authenticate(string $email, string $password):IIdentity{
    try{
      $user=$this->usersFacade->getUserByEmail($email);
    }catch (\Exception $e){
      //uživatel nebyl nalezen
      throw new AuthenticationException('Uživatelský účet neexistuje.');
    }

    if ($this->passwords->verify($password, $user->password)){
      //hash hesla byl ověřen
      $roles=['authenticated'];
      if (!empty($user->role)){
        $roles[]=$user->role->roleId;
      }
      return new SimpleIdentity($user->userId,$roles,['name'=>$user->name,'email'=>$user->email]);
    }else{
      throw new AuthenticationException('Chybná kombinace e-mailu a hesla.');
    }
  }
}