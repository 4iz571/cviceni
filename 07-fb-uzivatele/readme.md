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

### Ukázková aplikace notes4
:mega:
1. stáhněte si soubor s exportem databáze a naimportujte jeho obsah do MariaDB
   - pokud máte databázi z minulé hodiny, naimportujte jen soubor **[notes4-diff-db.sql](./notes4-diff-db.sql)** 
   - případně celou strukturu databáze najdete v souboru **[notes4-db.sql](./notes4-db.sql)** 
2. stáhněte si složku **[notes4](./notes4)** s ukázkovým projektem, nahrajte jej na server (nezapomeňte na úpravu práv k adresářům log a temp)
3. otevřete si ukázkové projekty ve vývojovém prostředí
4. v souboru **config/local.neon** přístupy k databázi

:point_right:

**Aktuální stav aplikace:**
Oproti stavu z 6. cvičení ([notes3](../06-uzivatele-maily/notes3)) už je aplikace připravena na možnost obnovy zapomenutého hesla: 
- byly doplněny potřebné formuláře pro zadání e-mailu a změnu hesla
- byla vytvořena entita ForgottenPassword a odpovídající tabulka v DB, repozitář atd.
- v DB byla doplněna potřebná oprávnění v tabulce *permission* (aby měl nepřihlášený uživatel možnost obnovy hesla)

:mega:

**Doplnění funkcionalit:**
- je potřeba doplnit chybějící funkcionalitu týkající se poslání mailu s odkazem pro změnu hesla, jeho odeslání a ověření

:orange_book:
- [prezentace s postupem přípravy na obnovu hesla](./notes4-obnova-hesla-priprava.pptx) - výsledkem je aplikace ve verzi *notes4*, se kterou máme nyní pracovat
- [prezentace s postupem dokončení obnovy hesla](./notes4-obnova-hesla-dokonceni.pptx) - úpravy prováděné na cvičení




Společně se postupně podíváme na:
- přihlášení pomocí facebooku
- omezení úprav příspěvky podle role uživatele





:blue_book:
- https://github.com/facebookarchive/php-graph-sdk


```
composer require facebook/graph-sdk
```