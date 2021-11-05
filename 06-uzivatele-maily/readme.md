# 6. Uživatelé, posílání e-mailů

## Autentifikace uživatelů
:point_right:
- autentifikace/autentizace = prokázání identity uživatele
- autorizace = ověření, zda uživatelů může provádět konkrétní operaci
- **Jaké metody autentizace znáte?**

### Uložení uživatelských účtů do databáze
:point_right:
- pro přihlášení uživatelů potřebujeme uložit minimáně uživatelské heslo či e-mail + heslo
- heslo nikdy neukládáme v databázi v nešifrované podobě!
    - pro datový sloupec v DB je doporučená délka 255 znaků, aby bylo možné uložit všechny varianty hashů 

:point_right:
- pro šifrování hesel použijeme v Nette třídu Nette\Security\Passwords, kterou si ideálně necháme předat jako závislost, nebo jednoduše vyrobíme její instanci v místě použití
    - hesla nemusíme nijak upravovat, přidávat k nim sůl atp. - to za nás vyřeší tato třída
    - tato třída následně obsahuje metody **hash** a **verify**, které můžeme použít k zakódování hesla a ověření, zda heslo odpovídá hashi uloženému v databázi
    - při změně typu šifrování atp. je vhodné využít také metodu **needsRehash**, která umožní přihlásit uživatele se starší variantou hesla a poté heslo můžeme zakódovat po novu

```php
$passwords = new \Nette\Security\Passwords(PASSWORD_BCRYPT, ['cost' => 12]);
//uložení hesla při registraci uživatele či při změně hesla
$hash = $passwords->hash($zadaneHeslo);

//ověření hesla při přihlášení uživatele
if ($passwords->verify($zadaneHeslo)){
  //TODO přihlášení uživatele
}
```

:blue_book:
- [Hashování hesel na webu Nette](https://doc.nette.org/cs/3.1/passwords)

### Jak na přihlášení uživatele v Nette aplikaci?
1. v aplikaci implementujeme třídu implementující interface *Nette\Security\Authenticator* (např. v rámci samostatné třídy či v rámci UsersFacade v modelu)
    - bude obsahovat metodu **authenticate**, která bude vracet tzv. **identitu** (id uživatele, jeho role + další data, např. jméno, e-mail...)

    ```php
    class Authenticator implements Nette\Security\Authenticator {
      public function authenticate(string $user,string $password) : \Nette\Security\IIdentity{
        //TODO ověření jména a hesla    
      
        return new \Nette\Security\SimpleIdentity($userId, null, ['name'=>$userName]);
      }
    }
    ```
2. pro přihlášení uživatele poté využijeme objekt **Nette\Security\User**
    - do komponent si ji předáme jako závislost
    - v presenterech můžeme využít ```$this->user```, v šablonách poté proměnnou ```$user```

3. pokud chci zabezpečit presentery, mohu to zařídit např. v jejich metodě **startup**

   ```php
   protected function startup(){ 
     parent::startup();
     if (!$this->getUser()->isLoggedIn()){
       $this->redirect('Sign:in');
     }
   } 
   ```

### Ukázková aplikace notes2
:mega:
1. stáhněte si **[SQL soubor notes2-db.sql](./notes2-db.sql)** s exportem databáze a naimportujte jeho obsah do MariaDB (případné stejně pojmenované tabulky z předchozího příkladu smažte)
2. stáhněte si složku **[todolist](./todolist)** s ukázkovým projektem, nahrajte jej na server (nezapomeňte na úpravu práv k adresářům log a temp)
3. otevřete si ukázkové projekty ve vývojovém prostředí
4. v souboru **config/local.neon** přístupy k databázi

:mega:
5. aplikaci si vyzkoušejte a poté se podívejte na zdrojový kód
6. zkusme upravit aplikaci tak, aby pro přístup do aplikace bylo vyžadováno přihlášení
 
## Co může uživatel v databázi dělat?
