# 2. Model aplikace

## Opakování
:point_right:
- Na minulém cvičení jsme se bavili o návrhovém vzoru **MVP** - o co jde?

## Nezbytné konstrukty v Latte šablonách
:point_right:
- na minulém cvičení jsme si ukázali základní výpis proměnných a princip bloků
- podrobně se budeme Latte věnovat ještě [na příštím hodině](../04-latte-komponenty-formulare)
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
  <a href="{link vote! id=>$id}">hlasovat</a>
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
TODO

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

:blue_book:
- [web LeanMapperu](https://leanmapper.com/)

### Struktura modelu aplikace
:point_right:
- **Facades** = složka obsahující jednotlivé fasády - třídy, které ve skutečnosti načítáme do presenterů 
- **Entities** = složka s třídami entit
    - všimněte si, že dané neobsahují skoro žádný kód
    - mapování sloupců z databáze a vazeb mezi tabulkami je definováno v dokumentačním komentáři na začátku třídy
- **Repositories** = složka s repositáři, které slouží pro načítání a ukládání entit
    - většina kódu je v *BaseRepository*, do dalších repositářů napíšeme jen případná specifika pro daný typ entity
- **Mappers** 
    - složka obsahuje mapper, který převádí názvy properties na sloupce v databázi, názvy tříd na názvy tabulek atd.
    - v rámci tohoto příkladu využíváme vlastní mapper, který
        - v databázi využívá podtržítkovou syntaxi a pro názvy properties camelCase
        - pro primární klíče využívá název tabulky doplněný o "_id"
    - když vhodně přizpůsobíme názvy tabulek a jejich sloupců, jde využít i výchozí mapper přibalený v LeanMapperu                
    
### Co musíme udělat pro zprovoznění LeanMapperu v aplikaci
:point_right:
1. načtení LeanMapperu pomocí composeru
    ```shell script
    composer require tharos/leanmapper
    ```
2. upravíme common.neon - přidáme základ leanmapperu jako služby
    ```neon
   services:
   	- App\Router\RouterFactory::createRouter
   
   	- LeanMapper\Connection(%database%)
   	- App\Model\Mappers\StandardMapper('App\Model\Entities')
   	- LeanMapper\DefaultEntityFactory
    ```
3. příprava základní struktury modelu
    - v našem případě [BaseRepository](./notes-leanmapper/app/Model/Repositories/BaseRepository.php) a [StandardMapper](./notes-leanmapper/app/Model/Mappers/StandardMapper.php)
4. vytvoříme příslušnou strukturu databáze, pro každou tabulku poté vytvoříme *entitu* a *repozitář*
5. volitelně můžeme vytvořit obalovací fasády
6. všechny repozitáře a fasády přidáme do konfigurace aplikace, aby se v případě potřeby samy načítaly jako závislosti

### Úkoly pro procvičení
:mega:
1. spusťte aplikaci na serveru a prohlédněte si její zdrojový kód
2. zkuste doplnit správný kód na místa TODO komentářů ve třídě CategoryPresenter (vytvoření nové kategorie, úprava kategorie)
3. zkuste doplnit do aplikace vhodný kód tak, aby bylo možné smazat zvolenou kategorii 
4. vytvořte fasádu pro práci s poznámkami
5. zkuste doplnit presenter a příslušné šablony, který bude umět vypsat buď přehled všech poznámek, nebo přehled poznámek ve zvolené kategorii        