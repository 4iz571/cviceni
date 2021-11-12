# 7. Zapomenuté heslo, Facebook login, ACL


### Ukázková aplikace notes4
:mega:
1. stáhněte si soubor s exportem databáze a naimportujte jeho obsah do MariaDB
  - pokud máte databázi z minulé hodiny, naimportujte jen soubor **[notes4-diff-db.sql](./notes4-diff-db.sql)** 
  - případně celou strukturu databáze najdete v souboru **[notes4-db.sql](./notes4-db.sql)** 
2. stáhněte si složku **[notes4](./notes4)** s ukázkovým projektem, nahrajte jej na server (nezapomeňte na úpravu práv k adresářům log a temp)
3. otevřete si ukázkové projekty ve vývojovém prostředí
4. v souboru **config/local.neon** přístupy k databázi

:mega:
Společně se postupně podíváme na:
- obnovu zapomenutého hesla (a vyzkoušíte si poslání mailu, které jste zkoumali doma)
- přihlášení pomocí facebooku
- omezení úprav příspěvky podle role uživatele





:blue_book:
- https://github.com/facebookarchive/php-graph-sdk


```
composer require facebook/graph-sdk
```