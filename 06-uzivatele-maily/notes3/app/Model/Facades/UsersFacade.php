<?php

namespace App\Model\Facades;

use App\Model\Entities\Permission;
use App\Model\Entities\Resource;
use App\Model\Entities\Role;
use App\Model\Entities\User;
use App\Model\Repositories\PermissionRepository;
use App\Model\Repositories\ResourceRepository;
use App\Model\Repositories\RoleRepository;
use App\Model\Repositories\UserRepository;

/**
 * Class UsersFacade
 * @package App\Model\Facades
 */
class UsersFacade{
  private UserRepository $userRepository;
  private PermissionRepository $permissionRepository;
  private RoleRepository $roleRepository;
  private ResourceRepository $resourceRepository;

  public function __construct(UserRepository $userRepository, PermissionRepository $permissionRepository, RoleRepository $roleRepository, ResourceRepository $resourceRepository){
    $this->userRepository=$userRepository;
    $this->permissionRepository=$permissionRepository;
    $this->roleRepository=$roleRepository;
    $this->resourceRepository=$resourceRepository;
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
  public function saveUser(User &$user) {
    return (bool)$this->userRepository->persist($user);
  }

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