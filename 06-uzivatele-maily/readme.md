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
if ($passwords->verify($zadaneHeslo, $hash)){
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
2. stáhněte si složku **[notes2](./notes2)** s ukázkovým projektem, nahrajte jej na server (nezapomeňte na úpravu práv k adresářům log a temp)
3. otevřete si ukázkové projekty ve vývojovém prostředí
4. v souboru **config/local.neon** přístupy k databázi

:mega:

5. aplikaci si vyzkoušejte a poté se podívejte na zdrojový kód
6. zkusme upravit aplikaci tak, aby pro přístup do aplikace bylo vyžadováno přihlášení
 
## Co může uživatel v databázi dělat? A jak to zkontrolujeme?
:point_right:
V zásadě máme 3 základní varianty rozdělení uživatelských práv:
1. pouze kontrolujeme, jestli je uživatel přihlášen a např. u příspěvků kontrolujeme ID uživatele
2. oprávnění na základě **rolí** (ať už má uživatel jednu, nebo jich má přiřazeno více)
    - při přihlášení uživatele mu přiřadíme jednu či několik rolí
    - kontrolujeme, jestli uživatel má danou roli - např.
        ```php
        $this->user->isInRole('admin');
        ```
3. oprávnění postavené na **kombinaci rolí a zdrojů**
    - v rámci aplikace implementujeme autorizátor, který ověřuje oprávnění ke konkrétním činnostem nad zvoleným zdrojem - např.: 
        ```php
        $user->isAllowed('Note','edit');  
        ```

### Definice rolí a zdrojů
:point_right:
- pro větší flexibilitu doporučuji si uložit seznam rolí, zdrojů a oprávnění do databáze
- v zásadě potřebujeme:
    - tabulku **role**
        - nutně bude obsahovat jen role_id, ale volitelně tam mohou být např. jejich smysluplné názvy pro zobrazení v administraci
    - tabulku **resource**
        - bude obsahovat seznam zdrojů (tj. resource_id)
    - tabulku **permission**
        - bude obsahovat oprávění rolí ke konkrétním činnostem se zdroji

:point_right:
- vhodným typem "zdroje" jsou presentery, vykonávané činnosti jsou pak jednotlivé akce
- dalším vhodným typem "zdroje" jsou jednotlivé entity, u kterých potřebujeme individuálně kontrolovat oprávnění - např. může jít o poznámku na nástěnce, kterou by mohl editovat jen její autor
        
### Ukázková aplikace notes3
:mega:
1. stáhněte si **[SQL soubor notes3-diff-db.sql](./notes3-diff-db.sql)** s exportem databáze a naimportujte jeho obsah do MariaDB (jde o další část databáze k verzi [notes2](./notes2-db.sql))
2. prozkoumejte strukturu nově přidaných tabulek
2. stáhněte si složku **[notes3](./notes3)** s ukázkovým projektem, nahrajte jej na server (nezapomeňte na úpravu práv k adresářům log a temp)
3. otevřete si ukázkové projekty ve vývojovém prostředí
4. v souboru **config/local.neon** přístupy k databázi

:mega:

5. podívejte se na implementaci autorizátoru (třída *App\Model\Authorizator\Authorizator*)
    - autorizátor je automaticky načten díky DI do objektu *user*, používá se pro ověřování oprávnění pomocí ```$user->isAllowed($resource, $action)```
    - v rámci této aplikace jsou zdroji jednotlivé presentery
6. zkuste doplnit do aplikace přiřazení role uživateli a její načtení v autentizátoru    

---

## Posílání mailů
:point_right:
- posílání mailů budeme potřebovat např. k obnově zapomenutého hesla, ale bude se hodit např. také pro posílání potvrzení objednávky z e-shopu, rozesílání novinek atp.
- většinou dnes posíláme maily v HTML, ale neměli bychom zapomínat na to, že se musí přijatelně zobrazit ve všech e-mailových klientech (nemůžeme počítat s plnou podporou stylů atp.)
- pozor, když budete posílat maily ze serveru eso.vse.cz, pamatujte, že je jde posílat jen na adresy končící na @vse.cz 

:blue_book:
- abychom se trochu seznámili s webem Nette, zkuste se podívat na něj na  [informace k odesílání mailů](https://doc.nette.org/cs/3.1/mailing)

:point_right:
Malá ochutnávka možností:
```php
//sestavení mailu
$mail = new Nette\Mail\Message();
$mail->setFrom('xname@vse.cz','Jméno odesílatele');
$mail->addTo('xname@vse.cz','Jméno adresáta');
$mail->subject = 'Ukázkový mail';
$mail->htmlBody = 'Obsah <strong>ukázkového mailu</strong>';
$mail->addAttachment('dokument.pdf',file_get_contents(__DIR__.'/dokument.pdf'));

//možnost poslání pomocí PHP funkce mail
$mailer = new Nette\Mail\SendmailMailer;
$mailer->send($mail);

//možnost poslání přes normální SMTP server
$smtpMailer = new Nette\Mail\SmtpMailer([
	'host' => 'smtp.gmail.com',
	'username' => 'user@gmail.com',
	'password' => '*****',
	'secure' => 'ssl',
]);
$smtpMailer->send($mail);
```