<?php

namespace App\Model\Repositories;

use App\Model\Entities\Todo;

/**
 * Class TodoRepository - repozitář pro ukládání jednotlivých úkolů
 * @package App\Model\Repositories
 * @method Todo|null find(int $id)
 * @method Todo|null findBy(array $conditions=null,int $offset=null,int $limit=null)
 * @method Todo[] findAllBy(array $conditions=null,int $offset=null,int $limit=null)
 */
class TodoRepository extends BaseRepository{

}