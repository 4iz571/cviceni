<?php

namespace App\Model\Repositories;

/**
 * Class ForgottenPasswordRepository - repozitář pro "obnovu zapomenutých hesel"
 * @package App\Model\Repositories
 */
class ForgottenPasswordRepository extends BaseRepository{

  /**
   * Metoda pro smazání záznamů pro konkrétního uživatele
   */
  public function deleteForgottenPasswordsByUserId(int $user):void {
    $this->connection->delete($this->getTable())->where(['user_id'=>$user])->execute();
  }

  /**
   * Metoda pro smazání již neplatných záznamů
   */
  public function deleteOldForgottenPasswords():void {
    $this->connection->nativeQuery('DELETE FROM `'.$this->getTable().'` WHERE created < (NOW() - INTERVAL 1 HOUR)');
  }

}