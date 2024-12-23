<?php

namespace App\Model\Facades;

use App\Model\Entities\ForgottenPassword;
use App\Model\Entities\Permission;
use App\Model\Entities\Resource;
use App\Model\Entities\Role;
use App\Model\Entities\User;
use App\Model\Repositories\ForgottenPasswordRepository;
use App\Model\Repositories\PermissionRepository;
use App\Model\Repositories\ResourceRepository;
use App\Model\Repositories\RoleRepository;
use App\Model\Repositories\UserRepository;
use LeanMapper\Exception\InvalidStateException;
use Nette\Utils\Helpers;
use Nette\Utils\Random;

/**
 * Class UsersFacade
 * @package App\Model\Facades
 */
class UsersFacade{
  private UserRepository $userRepository;
  private PermissionRepository $permissionRepository;
  private RoleRepository $roleRepository;
  private ResourceRepository $resourceRepository;
  private ForgottenPasswordRepository $forgottenPasswordRepository;

  public function __construct(UserRepository $userRepository, PermissionRepository $permissionRepository,
                              RoleRepository $roleRepository, ResourceRepository $resourceRepository,
                              ForgottenPasswordRepository $forgottenPasswordRepository){
    $this->userRepository=$userRepository;
    $this->permissionRepository=$permissionRepository;
    $this->roleRepository=$roleRepository;
    $this->resourceRepository=$resourceRepository;
    $this->forgottenPasswordRepository=$forgottenPasswordRepository;
  }

  /**
   * Metoda pro načtení jednoho uživatele
   * @param int $id
   * @return User
   * @throws \Exception
   */
  public function getUser(int $id):User {
    return $this->userRepository->find($id);
  }

  /**
   * Metoda pro načtení jednoho uživatele podle e-mailu
   * @param string $email
   * @return User
   * @throws \Exception
   */
  public function getUserByEmail(string $email):User {
    return $this->userRepository->findBy(['email'=>$email]);
  }

  /**
   * Metoda pro uložení uživatele
   * @param User &$user
   * @return bool
   */
  public function saveUser(User &$user):bool {
    return (bool)$this->userRepository->persist($user);
  }

  #region metody pro zapomenuté heslo
  /**
   * Metoda pro vygenerování a uložení nového záznamu pro obnovu hesla
   * @param User $user
   * @return ForgottenPassword
   * @throws \LeanMapper\Exception\InvalidArgumentException
   */
  public function saveNewForgottenPasswordCode(User $user):ForgottenPassword {
    $forgottenPassword=new ForgottenPassword();
    $forgottenPassword->user=$user;
    $forgottenPassword->code=Random::generate(10);
    $this->forgottenPasswordRepository->persist($forgottenPassword);
    return $forgottenPassword;
  }

  /**
   * Metoda pro ověření, zda je platný zadaný kód pro obnovu uživatelského účtu
   * @param User|int $user
   * @param string $code
   * @return bool
   */
  public function isValidForgottenPasswordCode($user, string $code):bool {
    if ($user instanceof User){
      $user=$user->userId;
    }
    $this->forgottenPasswordRepository->deleteOldForgottenPasswords();
    try{
      $this->forgottenPasswordRepository->findBy(['user_id'=>$user, 'code'=>$code]);
      return true;
    }catch (\Exception $e){
      return false;
    }
  }

  /**
   * Metoda pro jednoduché smazání kódů pro obnovu hesla pro konkrétního uživatele
   * @param User|int $user
   */
  public function deleteForgottenPasswordsByUser($user):void{
    try{
      if ($user instanceof User){
        $user=$user->userId;
      }
      $this->forgottenPasswordRepository->delete(['user_id' => $user]);
    }catch (InvalidStateException $e){
      //ignore error
    }
  }
  #endregion metody pro zapomenuté heslo

  #region metody pro authorizator
  /**
   * @return Resource[]
   */
  public function findResources():array {
    return $this->resourceRepository->findAll();
  }

  /**
   * @return Role[]
   */
  public function findRoles():array {
    return $this->roleRepository->findAll();
  }

  /**
   * @return Permission[]
   */
  public function findPermissions():array {
    return $this->permissionRepository->findAll();
  }
  #endregion metody pro authorizator
}