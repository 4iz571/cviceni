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

## Příprava příkladu pro dnešní hodinu
:mega:
1. stáhněte si **[SQL soubor](./notes-db.sql)** s exportem databáze a naimportujte jeho obsah do MariaDB
2. stáhněte si obě složky (**notes-nettedb**, **notes-orm**) s ukázkovými projekty, nahrajte je na server (nezapomeňte na úpravu práv )
3. otevřete si ukázkové projekty ve vývojovém prostředí
3. v obou ukázkových příkladech upravte v souboru **config/local.neon** přístupy k databázi