# 12. E-shop - jiné typy výstupu (XML, JSON, PDF), AJAX, lokalizace  

## Jiné typy výstupu
:point_right:
- ne vždy chceme generovat výstup ve formátu HTML - např. pro komunikaci s javascriptem se hodí JSON či XML, sitemapa bude v XML atd.
- výstup v jiném formátu můžeme generovat buď prostřednictvím  latte šablony, ve které uvedeme jiný content type, nebo můžeme odpověď vygenerovat v presenteru

### Odeslání JSON/XML odpovědi rovnou z presenteru

### XML pomocí latte - sitemap.xml 
:point_right:
- sitemap.xml je základní rozcestník, který má vyhledávačům napovědět, jaké stránky na našem webu najde
    - v eshopu by to měly být stránky jednotlivých produktů, kategorií... 
- sitemap můžeme jednoduše vygenerovat přímo v aplikaci - základ k dopracování je uveden v ukázkové aplikaci

:point_right:
- v latte šabloně můžeme na jejím začátku uvést, jaký typ výstupu chceme generovat; následně pak generujeme potřebné XML značky
  ```latte
  {contentType application/xml}
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
      <url>
        <loc>{$baseUrl}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
      </url>
      
      {*TODO výpis jednotlivých produktů*}
      
    </urlset>
    ```
- sitemapa by ideálně měla být v ideálním stavu *sitemap.xml* v kořenové složce webu a zároveň zapsaná v *robots.txt*
    - pro namapování na presenter/akci můžeme použít buď podsunutí v .htaccessu, nebo jednoduše přidání další cesty v routeru:
    ```php
    $frontRouter = new RouteList('Front');
    $frontRouter->addRoute('sitemap.xml', 'Homepage:sitemap');
    ```

:blue_book:
- [Sitemap XML format](https://www.sitemaps.org)
- [XML Sitemap Validator](https://www.xml-sitemaps.com/validate-xml-sitemap.html)

:mega:
- dopracujte v ukázkové aplikaci výpis sitemapy (alespoň pro produkty)  

## AJAX prostřednictvím knihovny Naja
:point_right:
- v Nette najdeme podporu pro řešení ajaxového chování aplikace prostřednictvím překreslování pouze vybraných částí šablon
    - při prvním požadavku na danou stránku se přenese celá šablona
    - při dalších dílčích změnách se přenášejí jen vybrané části, které označujeme jako "snippety" 
- samotné překreslení části stránky musí vyřešit javascript, starý jednoduchý skript už ale není podporován - v současné době se používají [knihovna Naja](https://naja.js.org/) nebo [Nittro framework](https://www.nittro.org/)
    - budeme využívat knihovnu Naja, která se specializuje jen na ajaxová volání a je jednodušší 

:blue_book:
- [AJAX & snipety na webu Nette](https://doc.nette.org/cs/3.1/ajax)
- [Knihovna Naja](https://naja.js.org/)

### Snippety a jejich invalidace









### Ukázková aplikace

:mega:

Pro dokončení košíku vyjdeme z aplikace z minulého cvičení, do které bylo doplněno pár kousků kódu. Doporučuji si ji stáhnout z GITu:
1. stáhněte si soubor s [rozdílovým exportem databáze](./eshop-diff-db-kosik2.sql) a naimportujte jeho obsah do MariaDB (případně je k dispozici také soubor s [kompletním exportem databáze](./eshop-db.sql))
2. stáhněte si složku **[eshop](./eshop)** se zdrojovým kódem projektu, nahrajte její obsah na server (a nezapomeňte na úpravu práv k adresářům *log* a *temp*)
3. v souboru **config/local.neon** přístupy k databázi, později také přístupy k FB loginu
4. upravte práva k adresáři *www/img/products* (nastavte práva 777)