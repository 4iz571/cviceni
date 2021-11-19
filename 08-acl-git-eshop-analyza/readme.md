# 7. Dokončení ACL, GIT, analýza e-shopu 

## Dokončení ACL
:point_right:
- na minulých 2 cvičeních jsme se zabývali autentizací a autorizací uživatelů, přičemž pro ideální chování nám v aplikaci *notes* chybí jen pár drobných dodělávek
    - přesměrování uživatele na stránku, na kterou se snažil přistoupit jako nepřihlášený
    - kontrola oprávnění - příspěvek může upravovat jen jeho autor či admin
- navíc si ukážeme, jak lze do Authorizátoru začlenit i kontrolu stavu entity    

:mega:
Ukázkový příklad vychází z aplikace **notes5**, se kterou jsme pracovali na minulém cvičení
- pokud aplikaci dokončenou nemáte:
    1. stáhněte si soubor s exportem databáze z verze *notes5* ([tady](../07-fb-uzivatele/notes5-db.sql) a naimportujte jeho obsah do MariaDB
    2. stáhněte si složku **[notes6](./notes6)** s celým kódem projektu, nahrajte jej na server (nezapomeňte na úpravu práv k adresářům *log* a *temp*)
    3. v souboru **config/local.neon** přístupy k databázi a k FB aplikaci (i když bez těch to půjde také)
- **pokud máte aplikaci hotovou** z domácího úkolu (či jste absolvovali předchozí body):
    1. stáhněte si jen **[změněné soubory](./notes6-diff)** a nahrajte je na server
    2. nahrajte do databáze nově doplněná oprávnění ([SQL export](./notes6-diff-db.sql))    
          
### Oprávnění jen pro konkrétního uživatele a pro konkrétní entity
:point_right:
- kromě textového názvu role můžeme do "seznamu rolí" přidat také vlastní třídu implementující rozhraní *\Nette\Security\Role* - např.:
    - můžeme toto rozhraní přidat do entity *Role*:
        ```php
        /**
         * Class Role
         * @package App\Model\Entities
         * @property string $roleId
         * @property User $user m:belongsToOne
         */
        class Role extends Entity implements \Nette\Security\Role{
        
          /**
           * @inheritDoc
           */
          function getRoleId():string{
            return $this->roleId;
          }
        }
        ```
    - pro další typy oprávnění si můžeme vytvořit vlastní třídu např. pro roli "authenticated"
        - viz [AuthenticatedRole](./notes6-diff/app/Model/Authorizator/AuthenticatedRole.php)

:point_right:
- jako zdroj lze používat nejen řetězec, ale také třídu implementující *\Nette\Security\Resource*
     - takže se můžeme poté ptát na autorizaci např. u konkrétních entit v databázi
     - např.:
        ```php
        class Note extends Entity implements \Nette\Security\Resource{
        
          /**
           * @inheritDoc
           */
          function getResourceId():string{
            return 'Note';
          }
        }
        ```

### Kam umístit kontrolu, zda je dané oprávnění platné?
:point_right:
- **varianta 1** - implementujeme vlastní autorizátor, do kterého si doplníme patřičné podmínky do metody *isAllowed*
    - podívejte se na [Authorizator v notes6](./notes6-diff/app/Model/Authorizator/Authorizator.php)
    - např.:  
        ```php
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
              
            return parent::isAllowed($role, $resource, $privilege);
          }  
        ```
- **varianta 2** - při přidávání oprávnění (v rámci inicializace autorizátoru) doplníme podmínku k jednotlivým pravidlům
    - např.:
        ```php
        $assertion = function (Permission $acl, string $role, string $resource, string $privilege): bool {
            $role = $acl->getQueriedRole(); // objekt Registered
            $note = $acl->getQueriedResource(); // objekt Article
            return $role->userId === $note->author->userId;
        };
        
        $authorizator->allow('authenticated', 'Note', 'edit', $assertion);
        ```
### Uložení požadavku a jeho obnovení po přihlášení uživatele


## GIT
:point_right:
- Co je to "git"? K čemu je vlastně dobré jej používat?
- dnes jde v zásadě o standard při vývoji kódu
- jaký je rozdíl mezi "public" a "private" repozitářem?

:point_right:
- naklonování vzdáleného repozitáře:
    ```shell script
    git clone https://github.com/4iz571/cviceni.git mujAdresar
    ```
- stažení aktualizací z repozitáře:
    ```shell script
    git pull
    ```  
- aktuální stav změn:
    ```shell script
    git status
    ```
- přidání změněných souborů:
    ```shell script
    git add *
    git commit -m "popis"
    git push
    ```

:point_right:
- vhodné servery (pokud nechceme vlastní):
    - [https://github.com]
    - [https://bitbucket.org]
    - [https://gitlab.com]

:blue_book:
- [Základy systému Git](https://git-scm.com/book/cs/v2/%C3%9Avod-Z%C3%A1klady-syst%C3%A9mu-Git)