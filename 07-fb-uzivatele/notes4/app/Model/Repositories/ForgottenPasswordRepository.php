<?php

namespace App\Model\Repositories;

use App\Model\Entities\ForgottenPassword;

/**
 * Class ForgottenPasswordRepository - repozitář pro "obnovu zapomenutých hesel"
 * @package App\Model\Repositories
 * @method ForgottenPassword|null find(int $id)
 * @method ForgottenPassword|null findBy(array $conditions=null, int $offset=null, int $limit=null)
 * @method ForgottenPassword[] findAllBy(array $conditions=null, int $offset=null, int $limit=null)
 */
class ForgottenPasswordRepository extends BaseRepository{

  /**
   * Metoda pro smazání již neplatných záznamů
   */
  public function deleteOldForgottenPasswords():void{
    $this->connection->nativeQuery('DELETE FROM `forgotten_password` WHERE created < (NOW() - INTERVAL 1 HOUR)');
  }

}