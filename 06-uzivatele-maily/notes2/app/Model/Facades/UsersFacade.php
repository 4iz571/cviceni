<?php

namespace App\Model\Facades;

use App\Model\Entities\User;
use App\Model\Repositories\UserRepository;

/**
 * Class UsersFacade
 * @package App\Model\Facades
 */
class UsersFacade{
  private UserRepository $userRepository;

  public function __construct(UserRepository $userRepository){
    $this->userRepository=$userRepository;
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

}