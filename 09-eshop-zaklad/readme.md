# 9. Začátek práce na e-shopu  

## Struktura aplikace, datový model
:point_right:
- pojďme společně rozebrat vhodnou strukturu aplikace a datový model    

### Dělíme aplikaci do modulů
:point_right:
- v řadě aplikací narážíme na to, že se vlastně skládají z několika oddělených částí
  - např. v e-shopu je obvykle rozdělená část pro běžné zákazník a část pro administrátory (každá má jiné UI, nabízí jiné možnosti atd.)
  - dalším obvyklým modulem je v řadě aplikací nějaké API
- aplikaci můžeme jednoduše rozdělit do částí, které nazýváme "moduly"
  - *FrontModule* bude obsahovat presentery a komponenty pro zákaznickou část
  - *AdminModule* bude obsahovat presentery a komponenty pro administraci 
  
### Tvorba odkazů, kontrola práv
:point_right:
- v rámci kontroly oprávnění doplníme do názvu presenteru také název modulu - např.:
  ```php
  $user->isAllowed('Front:User','logout')
  ```
- při tvorbě odkazů v rámci jednoho modulu se nic nezmění:
  ```latte
  <a href="{plink User:logout}">odhlásit se</a>
  ```
- při odkazu na presenter z jiného modulu uvedeme celý název presenteru s dvojtečkou na začátku:
  ```latte
  <a href="{plink :Front:User:logout}">odhlásit se</a>
  ```
- při přesměrování v rámci presenteru:
  ```php
  $this-redirect(':Front:User:logout');
  ``` 

## Stylujeme aplikaci pomocí Bootstrapu
:point_right:
- v rámci základu aplikace je využito stylování pomocí [Bootstrap 4.6](https://getbootstrap.com/docs/4.6/)
  - tato verze byla vybrána z důvodu velké rozšířenosti využití, s trochou štěstí už jste se s ní setkali :)
- volitelně ale můžete využít také jakoukoliv jinou knihovnu

### Vykreslování formulářů  
:point_right:
- ať již použijeme jakoukoliv knihovnu pro CSS, bylo by vhodné ji využít také pro vykreslování formulářů
- každému formuláři jde jednoduše přiřadit renderer, který automaticky přidává příslušné obalovací značky a třídy k jednotlivým prvkům
- v rámci základu aplikace využijeme knihovnu **[nextras/forms-rendering](https://github.com/nextras/forms-rendering)**, alternativně ale jde formuláře vykreslit také ručně

:point_right:
- příklad vykreslení formuláře:
  ```php
  $form = new Form();
  $form->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
  ```
  
:blue_book:
- [Get started - Bootstrap 4.6](https://getbootstrap.com/docs/4.6/)
- [nextras/form-rendering](https://github.com/nextras/forms-rendering)
- [Vykreslování formulářů v Nette](https://doc.nette.org/cs/3.1/form-rendering)  


## Základ aplikace

:mega:

Pro začátek tvorby semestrální práce máte v podkladech pro toto cvičení připravený základ aplikace. Pro její spuštění:
1. stáhněte si soubor s exportem databáze ([eshop-db.sql](./eshop-db.sql)) a naimportujte jeho obsah do MariaDB
2. stáhněte si složku **[eshop](./eshop)** se základem projektu, nahrajte její obsah na server (a nezapomeňte na úpravu práv k adresářům *log* a *temp*)
3. otevřete si ukázkové projekty ve vývojovém prostředí
4. v souboru **config/local.neon** přístupy k databázi, později také přístupy k FB loginu
5. zaregistrujte si v aplikaci uživatelský účet a následně mu v databázi přiřaďte roli *admin*

:point_right:
- v základu projektu je hotové:
   - přihlašování uživatelů, kontrola práv
   - rozdělení aplikace na moduly
   - v administraci je hotový základ správy kategorií
- administraci najdete na adrese, do které doplníte */admin*, tj. například: https://eso.vse.cz/~xname/eshop/admin
- pro možnost jednoduchého smazání cache je v základu projektu skript *deleteCacheDir.php*, najdete ho např. na adrese https://eso.vse.cz/~xname/eshop/deleteCacheDir.php  

:mega:
- nahrajte základ aplikace do vlastního GIT repozitáře, který budete používat pro práci na semestrálce
- definujte strukturu databáze a odpovídající entity a repozitáře
- začněte pracovat na administrační části e-shopu (začněte správou nabízených položek a správou uživatelů)