<?php

namespace App\Model\Repositories;

/**
 * Class ForgottenPasswordRepository - repozitář pro "obnovu zapomenutých hesel"
 * @package App\Model\Repositories
 */
class ForgottenPasswordRepository extends BaseRepository{

  /**
   * Metoda pro smazání již neplatných záznamů
   */
  public function deleteOldForgottenPasswords(){
    $this->connection->nativeQuery('DELETE FROM `forgotten_password` WHERE created < (NOW() - INTERVAL 1 HOUR)');
  }

}