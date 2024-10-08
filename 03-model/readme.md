# 3. Model aplikace

## Opakování
:point_right:
- Na minulém cvičení jsme se bavili o návrhovém vzoru **MVP** - o co jde?

## Nezbytné konstrukty v Latte šablonách
:point_right:
- na minulém cvičení jsme si ukázali základní výpis proměnných a princip bloků
- podrobně se budeme Latte ještě více věnovat na dalších cvičeních
- na dnešní hodině se podíváme jen na pár základních konstruktů, které budeme potřebovat k ukázkové aplikaci

### Výpis proměnných
:point_right:
- přiřazení proměnné do šablony:
  ```php
  public function renderDefault():void { //akce v presenteru
    $this->template->ukazkovyText='<b>Lorem ipsum</b>';
  }
  ```
- výpis proměnné v šabloně (a zároveň ukázka komentáře v šabloně):
  ```latte
  {$ukazkovyText}   {* výpis proměné s ošetřením speciálních znaků *}
  {$ukazkovyText|noescape} {* výpis proměné BEZ ošetření speciálních znaků *}
  ```  
- dump proměnné pro účely ladění - výpis poté najdeme v Tracy
  ```latte
  {dump $ukazkovyText}
  ```
  
### Tvorba odkazů
:point_right:
- do odkazů nepíšeme přímo URL, ale odkazujeme na konkrétní akci, kterou chceme spustit
- odkaz na akci *show* v presenteru *Product*:
  ```latte
  <a n:href="Product:show id=>$id">detail produktu</a>
  <a href="{link Product:show id=>$id}">detail produktu</a>
  ```
- odkaz na signál (subrequest), který má obvykle provést nějakou činnost a poté se vrátit na původní akci:
  ```latte
  <a n:href="vote! id=>$id">hlasovat</a>
  <a href="{link vote! id=>$id}">hlasovat</a>
  ```      

### Podmínky
:point_right:
- podmínky jde buď zapisovat jako n: makra (pak se vztahují k danému elementu), nebo klasickým zápisem:
  ```latte
  {if $podminka}
      ...  
  {elseif}
      ...
  {else}
      ...
  {/if}
  
  <div n:if="$podminka">...</div>
  ```
- výpis prvku jen v případě, že obsahuje nějaký obsah:
  ```latte
  <div n:ifcontent>...</div>
  ```

### Cykly
:point_right:
- základní cykly:
  ```latte
  {for $i = 0; $i < 10; $i++}
    <span>Položka {$i}</span>
  {/for}
  
  {while $podminka}
  ...
  {/while}
  ```
- procházení kolekcí
  ```latte
  {foreach $array as $key=>$value}
    <span>Položka {$value}</span>
    ....
    {continue}
  
  {/foreach}
  
  {foreach $rows as $row}
    {first}<table>{/first}
      <tr id="row-{$iterator->counter}">
        <td>{$row->name}</td>
        <td>{$row->email}</td>
      </tr>
    {last}</table>{/last}
  {/foreach}
  ```

:blue_book:
- pro podrobný manuál můžete využít [Web Latte](https://latte.nette.org/)

## Příprava příkladů pro dnešní hodinu
:point_right:
Při dnešní hodině si prakticky vyzkoušíme úpravy modelu aplikace, přičemž se podíváme na 2 rozdílné implementace, respektive jednu a tu samou aplikaci implementovanou za využití různých variant modelu.

:mega:
1. stáhněte si **[SQL soubor](./notes-db.sql)** s exportem databáze a naimportujte jeho obsah do MariaDB
2. stáhněte si obě složky (**notes-nettedb**, **notes-leanmapper**) s ukázkovými projekty, nahrajte je na server (nezapomeňte na úpravu práv )
3. otevřete si ukázkové projekty ve vývojovém prostředí
3. v obou ukázkových příkladech upravte v souboru **config/local.neon** přístupy k databázi

## Model využívající Nette Database
:point_right:
- pro Nette je k dispozici databázová vrstva Nette Database, která usnadňuje tvorbu SQL dotazů (*Database Core*) a také procházení databáze na základě její struktury (*Database Explorer*)
- pro využití v projektu:
    1. načtěte balíček nette/database pomocí composeru
        ```shell script
        composer require nette/database
        ```
    2. doplňte do [local.neon](./notes-nettedb/config/local.neon) konfiguraci připojení k databázi
    3. do modelu, ve kterém budeme chtít databázi používat, si jako závislost předáme ```\Nette\Database\Connection``` 
        

### Database Core
:point_right:
- = nadstavbová vrstva nad PDO, která nám usnadňuje skládání SQL příkazů
- buď voláme metodu *query*, která spustí dotaz a vrací ResultSet, nebo voláme metody *fetch*, *fetchColumn* a *fetchAll*

:point_right:  
```php
$database->query('SELECT id FROM users ORDER BY', [
	'id' => true, // vzestupně
	'name' => false, // sestupně
]);

$database->fetchAll('SELECT * FROM users WHERE id = ?', $id);

$database->query('INSERT INTO users ?', [ 
	'name' => $name,
	'email' => $email
]);
$id = $database->getInsertId();
```    

:blue_book:
- [Database Core na webu Nette](https://doc.nette.org/cs/database/core) - doporučuji se podívat na příklady
- [Ukázkový příklad notes-nettedb](./notes-nettedb)

:orange_book:
- [Prezentace s postupem úprav notes-nettedb](./notes-nettedb-reseni.pptx)

### Database Explorer
:point_right:
- pokud si nerozumíte s SQL, nebo si prostě chcete výběry usnadnit, poskytuje Nette v rámci databázové vrstvy také **Database Explorer**
- jednotlivé dotazy se skládají pomocí tzv. *fluent* rozhraní  ```$table->where(...)->order(...)->limit(...)```
- umožňuje také výběry z tabulek navázaných přes cizí klíče - tj. výsledný objekt umí donačíst navázaná data

:point_right:
- pokud chceme Database Explorer použít, jednoduše si místo ```\Nette\Database\Connection``` necháme jako závislost příslušné třídy modelu předat ```\Nette\Database\Explorer```

```php
$user = $explorer->table('users')->where(email=?,$email);
echo $user->name;

$explorer->table('users')->insert([
  'name' => $name,
  'year' => $email
]);

$books = $explorer->table('book');
foreach ($books as $book) {
  echo $book->title . ': ';
  echo $book->author->name;
}
```

:blue_book:
- [Database Explorer na webu Nette](https://doc.nette.org/cs/database/explorer)

## Model využívající ORM pomocí LeanMapperu
:point_right:
- jeden z velmi populárních přístupů k vývoji větších aplikací je využití knihovny pro objektově-relační mapování (ORM)
- budeme využívat jednoduchou knihovnu **LeanMapper**
    - knihovna s relativně jednoduchým zdrojovým kódem, která má v závislostech jen Dibi
    - využívá návrhový vzor *Repository*
        - entita jen uchovává data a umí donačíst dílčí závislosti
        - repozitáře slouží k ukládání entit, změnám v DB atp.
- na příkladu si ukážeme spojení s návrhovým vzorem *Facade*
    - fasáda je třída, která zaštiťuje jeden či několik repozitářů a poskytuje tím ucelenou sadu funkcí pro volání z presenteru
    - v zásadě nás fasáda může odstínit od samotného zdroje dat - můžeme mít např. jednotně pojmenované metody pro získání dat, které máme v databázi a také těch, které načítáme přes API 

:blue_book:
- [web LeanMapperu](https://leanmapper.com/)
- [Ukázkový příklad notes-leanmapper](./notes-leanmapper)

### Struktura modelu aplikace
:point_right:
- **Facades** = složka obsahující jednotlivé fasády - třídy, které ve skutečnosti načítáme do presenterů 
- **Entities** = složka s třídami entit
    - všimněte si, že dané neobsahují skoro žádný kód
    - mapování sloupců z databáze a vazeb mezi tabulkami je definováno v dokumentačním komentáři na začátku třídy
- **Repositories** = složka s repositáři, které slouží pro načítání a ukládání entit
    - většina kódu je v *BaseRepository*, do dalších repositářů napíšeme jen případná specifika pro daný typ entity
    
### Objektově-relační mapování
:point_right:
- pro převod objektů na třídy a zpět je využíván tzv. "mapper"
- podrobněji se na mappery podíváme ještě na příštím cvičení
- v rámci tohoto projektu je využíván mapper s těmito konvencemi:
    - v kódu používáme camelCase syntaxi pro názvy tříd a properties, u tříd začínáme velkým písmenem
    - v databázi jsou všechny názvy malými písmeny, slova oddělena podtržítkem
    - repozitáře se jmenují např. *NoteRepository* (tj. za jméno entity doplníme "Repository")
    - primární klíče jsou pojmenovány jménem tabulky s doplněným "_id", tj. například *note_id*                        
    
### Co musíme udělat pro zprovoznění LeanMapperu v aplikaci
:point_right:
1. načtení LeanMapperu pomocí composeru + v tomto případě ještě balíček s mappery
    ```shell script
    composer require tharos/leanmapper
    composer require vojir/leanmapper-mappers 
    ```
2. upravíme [common.neon](./notes-leanmapper/config/common.neon) - přidáme základ leanmapperu jako služby
    ```neon
   services:
   	- App\Router\RouterFactory::createRouter
   
   	- LeanMapper\Connection(%database%)
   	- Vojir\LeanMapper\Mappers\CamelcaseUnderdashMapper('App\Model\Entities')
   	- LeanMapper\DefaultEntityFactory
    ```
3. do souboru [local.neon](./notes-leanmapper/config/local.neon) doplníme parametry pro připojení k databázi    
4. příprava základní struktury modelu
    - v našem případě [BaseRepository](./notes-leanmapper/app/Model/Repositories/BaseRepository.php)
5. vytvoříme příslušnou strukturu databáze, pro každou tabulku poté vytvoříme *entitu* a *repozitář*
6. volitelně můžeme vytvořit obalovací fasády
7. všechny repozitáře a fasády přidáme do konfigurace aplikace, aby se v případě potřeby samy načítaly jako závislosti

### Úkoly pro procvičení
:mega:
1. spusťte aplikaci na serveru a prohlédněte si její zdrojový kód
2. zkuste doplnit správný kód na místa TODO komentářů ve třídě CategoryPresenter (vytvoření nové kategorie, úprava kategorie) 
3. vytvořte fasádu pro práci s poznámkami
4. zkuste doplnit presenter a příslušné šablony, který bude umět vypsat buď přehled všech poznámek, nebo přehled poznámek ve zvolené kategorii

:orange_book:
- [Prezentace s postupem úprav notes-leanmapper](./notes-leanmapper-reseni.pptx)

---

## Smazání adresáře cache
:point_right:
- pár z vás narazilo na potřebu smazat adresář *temp/cache*, ale smazání přes sftp připojení k serveru eso.vse.cz nefunguje. Problém je v uživatelských právech, neboť dané soubory byly vytvořeny z PHP, které běží na serveru pod vlastním uživatelem
    - => smazat daný obsah může zase PHP
- nahrajte do složky *www* soubor [deleteCacheDir.php](./deleteCacheDir.php) a načtěte jej přes prohlížeč 