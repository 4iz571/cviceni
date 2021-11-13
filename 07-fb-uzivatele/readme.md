# 7. Zapomenuté heslo, Facebook login, ACL 

## Obnova zapomenutého hesla
:point_right:
- na minulém cvičení jsme se bavili o možnostech přihlašování uživatelů, přičemž pokud se má uživatel přihlašovat kombinací mailu (či jména) a hesla, je pravděpodobné, že časem heslo zapomene => bude jej potřebovat získat
- hesla ukládáme v aplikaci šifrovaně, nemůžeme tedy uživateli poslat původní heslo - můžeme mu ale poskytnout možnost heslo změnit
- budeme předpokládat, že pokud má uživatel přístup k e-mailu, se kterým se registroval, tak je to on (či ona :-))

### Postup při obnově hesla
:point_right:
1. uživateli zobrazíme formulář pro zadání e-mailu
2. pokud uživatel s daným e-mailem existuje, vygenerujeme kód pro změnu hesla (a uložíme ho s omezenou platností do databáze)
3. pošleme uživateli e-mail s odkazem na změnu hesla
    - odkaz by měl obsahovat ID či jinou identifikaci uživatele a náš vygenerovaný bezpečnostní kód
    - posílání mailů bylo úkolem z minulého cvičení ([zde](../06-uzivatele-maily#pos%C3%ADl%C3%A1n%C3%AD-mail%C5%AF))
4. pokud uživatel použije odkaz na změnu, ověříme, že pro něj máme v databázi uložený zadaný bezpečnostní kód (a zda je stále ještě platný)
    - pokud ano, zobrazíme formulář na změnu hesla a heslo změníme
5. při úspěšném přihlášení uživatele smažeme všechny bezpečnostní kódy, které pro něj máme uložené (když si vzpomněl na heslo, tak už obnovu nepotřebuje :-))     

## Ukázková aplikace notes4
:mega:
1. stáhněte si soubor s exportem databáze a naimportujte jeho obsah do MariaDB
   - pokud máte databázi z minulé hodiny, naimportujte jen soubor **[notes4-diff-db.sql](./notes4-diff-db.sql)** 
   - případně celou strukturu databáze najdete v souboru **[notes4-db.sql](./notes4-db.sql)** 
2. stáhněte si složku **[notes4](./notes4)** s ukázkovým projektem, nahrajte jej na server (nezapomeňte na úpravu práv k adresářům *log* a *temp*)
3. otevřete si ukázkové projekty ve vývojovém prostředí
4. v souboru **config/local.neon** přístupy k databázi

### Aktuální stav aplikace
:point_right:

Oproti stavu z 6. cvičení ([notes3](../06-uzivatele-maily/notes3)) už je aplikace připravena na možnost obnovy zapomenutého hesla: 
- byly doplněny potřebné formuláře pro zadání e-mailu a změnu hesla
- byla vytvořena entita ForgottenPassword a odpovídající tabulka v DB, repozitář atd.
- v DB byla doplněna potřebná oprávnění v tabulce *permission* (aby měl nepřihlášený uživatel možnost obnovy hesla)


### Doplnění funkcionalit
:mega:
- je potřeba doplnit chybějící funkcionalitu týkající se poslání mailu s odkazem pro změnu hesla, jeho odeslání a ověření

:orange_book:
- [prezentace s postupem přípravy na obnovu hesla](./notes4-obnova-hesla-priprava.pptx) - výsledkem je aplikace ve verzi *notes4*, se kterou máme nyní pracovat
- [prezentace s postupem dokončení obnovy hesla](./notes4-obnova-hesla-dokonceni.pptx) - úpravy prováděné na cvičení


## Přihlašování uživatelů pomocí Facebooku
:point_right:
- přihlašování pomocí Facebooku, Google, Microsoftu, GitHubu atp. je populární, protože si nemusí uživatelé pamatovat heslo k naší aplikaci, ale jednoduše jen povolí, aby nám byly při přihlášení předány jejich údaje)
    - většina těchto poskytovatelů používá protokol OAuth 2
- pro přihlášení uživatele obvykle potřebujeme alespoň ID uživatele a jeho e-mail
    - ID uživatele doporučuji ukládat normálně do tabulky s uživateli (v našem příkladu tabulka *user*), pro každou sociální síť do samostatného sloupce
- pokud poskytujeme více možností přihlášení, měl by se uživatel vždy dostat k jednomu, "svému" uživatelskému účtu - bez ohledu na to, zda se přihlásil heslem, Facebookem či nějak jinak

### Postup přihlášení pomocí Facebooku (a dalších sociálních sítí)
:point_right:
1. uživatel klikne v naší tlačítko na odkaz pro přihlášení
2. sestavíme požadavek na přihlášení (URL sociální sítě s příslušnými parametry) a uživatele na tuto adresu přesměrujeme
3. ověření identity uživatele je starostí poskytovatele (sociální sítě)
4. po přihlášení (či jeho odmítnutí) je uživatel přesměrován zpět do naší aplikace
5. ověříme, jestli je uživatel přihlášen a ze sociální sítě si vyžádáme údaje o uživateli
    - doporučuji využít SDK vytvořené danou sítí, ale existují i univerzální knihovny atp.
    - z hlediska skutečného postupu:
        1. získáme access token
        2. pomocí access tokenu získáme základní metadata
        3. vyžádáme si další potřebné údaje (např. mail)  

:blue_book:
- [PHP Graph SDK od Facebooku](https://github.com/facebookarchive/php-graph-sdk)

#### Registrace aplikace na Facebooku
:point_right:
- pro využívání externí autentizace musíme naši aplikaci zaregistrovat u dané sociální sítě
    - musíme nastavit adresy aplikace, název, logo atp.
    - získáme ID aplikace a bezpečnostní kód, které poté musíme využívat při přístupu k API dané sítě 

:blue_book:
- [Facebook Developers](https://developers.facebook.com)
- [Google Sign-in - návod](https://developers.google.com/identity/sign-in/web/sign-in)

### Jak to implementovat ve vlastní aplikaci?
:point_right:
- buď využijeme již existující balíček (např. pro Nette může jít o [contributte/facebook](https://componette.org/contributte/facebook/)), nebo použijeme rovnou SDK a potřebné funkce si dopíšeme sami
- v rámci našeho příkladu zkusíme jít ruhou cestou - chceme jen přihlášení a nebudeme do aplikace nutně vkládat další závislosti

:point_right:
1. načteme do aplikace balíček příslušného SDK
    ```
    composer require facebook/graph-sdk
    ```
2. v rámci modelu si vytvoříme wrapper, který bude SDK využívat - respektive zpřístupní jeho potřebné funkce presenterům a komponentám
    - oddělení je výhodné v případě pozdější změny SDK (respektive API sociální sítě) - změny pak bude potřeba udělat jen na jednom místě
3. při přihlášení buď uživatele najdeme, nebo zaregistrujeme a následně vytvoříme instanci ```IIdentity```, kterou využijeme pro přihlášení (obejdeme autentizátor, který kontroluje heslo)

### Ukázková aplikace notes5
:point_right:
- v tomto adresáři najdete rozpracovanou další verzi aplikace, ve které je téměř hotové přihlašování pomocí Facebooku

:mega:
1. stáhněte si soubor s exportem databáze a naimportujte jeho obsah do MariaDB
   - pokud máte databázi z verze *notes4*, naimportujte jen soubor **[notes5-diff-db.sql](./notes5-diff-db.sql)** 
   - případně celou strukturu databáze najdete v souboru **[notes5-db.sql](./notes5-db.sql)** 
2. stáhněte si složku **[notes5](./notes5)** s ukázkovým projektem, nahrajte jej na server (nezapomeňte na úpravu práv k adresářům *log* a *temp*)
3. otevřete si ukázkové projekty ve vývojovém prostředí
4. v souboru **config/local.neon** přístupy k databázi     

:mega: :orange_book:
5. projděte si [prezentaci s postupem implementace přihlašování pomocí Facebooku](./notes5-fb-postup.pptx)
6. pokuste se přihlašování dokončit sami, případně podle konce prezentace zmíněné v předchozím bodu
