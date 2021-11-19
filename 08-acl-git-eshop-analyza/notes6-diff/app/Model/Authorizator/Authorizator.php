<?php

namespace App\Model\Authorization;

use App\Model\Authorizator\AuthenticatedRole;
use App\Model\Entities\Note;
use App\Model\Entities\Permission;
use App\Model\Facades\UsersFacade;
use Nette\Security\Role;

/**
 * Class Authorizator
 * @package App\Model\Authorization
 * @author Stanislav Vojíř
 */
class Authorizator extends \Nette\Security\Permission {

  /**
   * Metoda pro ověření uživatelských oprávnění
   * @param Role|string|null $role
   * @param \Nette\Security\Resource|string|null $resource
   * @param string|null $privilege
   * @return bool
   */
  public function isAllowed($role=self::ALL, $resource=self::ALL, $privilege=self::ALL):bool {

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