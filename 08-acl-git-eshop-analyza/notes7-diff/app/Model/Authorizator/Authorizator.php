<?php

namespace App\Model\Authorizator;

use App\Model\Entities\Note;
use App\Model\Entities\Permission;
use App\Model\Facades\UsersFacade;
use Nette\Security\Resource;
use Nette\Security\Role;

/**
 * Class Authorizator
 * @package App\Model\Authorization
 * @author Stanislav Vojíř
 */
class Authorizator extends \Nette\Security\Permission {

  /**
   * Metoda pro ověření uživatelských oprávnění
   */
  public function isAllowed(string|Role|null $role=self::All, string|Resource|null $resource=self::All, ?string $privilege=self::All):bool {

    //TODO tady mohou být kontroly pro jednotlivé entity

    if ($resource instanceof Note){
      #region ukázka omezení dle stavu entity - nejde upravovat ani mazat poznámku starší než 5 dnů
      if ($resource->updated->getTimestamp()<(time()-5*24*3600)){
        return false; //zamítneme oprávnění bez ohledu na roli
      }
      #endregion

      #region role "authenticated" může upravovat a mazat jen své vlastní příspěvky
      if ($role instanceof AuthenticatedRole){
        if ($privilege=='edit' || $privilege=='delete'){
          return ($resource->author->userId==$role->userId);
        }
      }
      #endregion
    }

    return parent::isAllowed($role, $resource, $privilege);
  }


  /**
   * Authorizator constructor - načte kompletní strukturu oprávnění
   * @param UsersFacade $usersFacade
   */
  public function __construct(UsersFacade $usersFacade){
    foreach ($usersFacade->findResources() as $resource){
      $this->addResource($resource->resourceId);
    }

    foreach ($usersFacade->findRoles() as $role){
      $this->addRole($role->roleId);
    }

    foreach ($usersFacade->findPermissions() as $permission){
      if ($permission->type==Permission::TYPE_ALLOW){
        $this->allow($permission->roleId,$permission->resourceId,$permission->action?$permission->action:null);
      }else{
        $this->deny($permission->roleId,$permission->resourceId,$permission->action?$permission->action:null);
      }
    }
  }

}