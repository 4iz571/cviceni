<?php

namespace App\Model\Repositories;

/**
 * Class CartRepository
 * @package App\Model\Repositories
 */
class CartRepository extends BaseRepository{

  /**
   * Metoda pro smazání již neplatných záznamů
   */
  public function deleteOldCarts():void {
    $this->connection->nativeQuery('DELETE FROM `cart` WHERE (user_id IS NULL AND last_modified < (NOW() - INTERVAL 30 DAY)) OR (last_modified < (NOW() - INTERVAL 3 DAY))');
  }

}