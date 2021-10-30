# 5. Latte, procvičení komponent a formulářů 

## Latte filtry a makra
### Latte filtry
:point_right:
- vypisování hodnot do UI rozhodně patří do šablony - co když ale budu chtít například datum či číslo naformátovat, doplnit do textu z formuláře zalomení řádků atp.?
- v Latte máme k dispozici celou řadu vestavěných **[filtrů](https://latte.nette.org/cs/filters)**, kterými můžeme
    - doupravit hodnoty při jejich výpisu
    - upravovat některé proměnné jako takové - např. pomocí filtru *sort* seřadit pole
- volitelně si můžeme doplňovat také filtry vlastní
    
:point_right:
```latte
{$text|noescape} {*filtr noescape vypne ošetření speciálních znaků ve výstupu*}
{$date|date:'j.n.Y'} {*filtr pro naformátování data*}
{$text|shortify:100} {*zkrácení řetězce na zadanou délku - parametry píšeme za dvojtečku*}
{$text|stripHtml|breakLines} {*postupně spuštěné filtry pro ořezání HTML značek a převod zalomení řádků na <br>*}
{='demo'|trim|upper} {*postupně spuštěné filtry trim a poté upper; všimněte si rovnítka jako makra pro výpis, pokud chceme vypisovat něco jiného, než proměnnou*}
```

:blue_book:
- [Přehled tagů na webu Nette](https://latte.nette.org/cs/tags)

### Další latte makra
:point_right:
- zatím jsme se na předchozích cvičeních seznámili jen s nutnými makry pro podmínky, bloky a odkazy - ale ono je jich o trochu víc...
- zkuste se podívat na přehled maker na webu Nette
- makra pro práci s proměnnými:
    ```latte
    {var string $name = $article->getTitle()} {*definice nové proměnné  - datový typ je volitelný*}
    
    {default int $id = 0} {*definice proměnné, pokud není definovaná*}
  
    {capture $var}<em>Hello World</em>{/capture} {*zachycení výstupu do proměnné*}
    <p>Captured: {$var}</p>
    ```
- makro pro překlady (zatím jen informačně, začlenění překladů si ještě vysvětlíme):
    ```latte
    {_'Lorem ipsum'}
    {_}Lorem ipsum{/_}
    ```
- makro pro změnu content typu na výstupu
    ```latte
    {contentType application/xml}
    <?xml version="1.0"?>
    <rss version="2.0">
    	<channel>
    		<title>RSS feed</title>
    		<item>
    			...
    		</item>
    	</channel>
    </rss>
    ```  

:blue_book:
- [Přehled maker (tagů) na webu Nette](https://latte.nette.org/cs/tags)

### Další poznámky k Latte
:point_right: :blue_book:
- podle zvoleného vývojového prostředí doporučuji nainstalovat odpovídající pluginy ([návod zde](https://latte.nette.org/cs/integrations))
- volitelně se podívejte na možnosti [definice datových typů pro šablony](https://latte.nette.org/cs/type-system)
    - na cvičeních si vystačíme s ```{varType string $text}```

---

## Todolist
:mega:
1. stejně jako na minulém cvičení si stáhněte **[ukázkový příklad pro tuto hodinu](./todolist2)**, nahrajte jej na server a zprovozněte (config, práva k adresářům)
2. doplňme do šablony s výpisem úkolů možnost jejich filtrování podle tagů, zkuste doplnit také stránkování
3. vytvořte formulář pro vytvoření/úpravu úkolu (entity **Todo**)

### Filtrování příspěvků podle vazby na tag
:point_right:
- zatím jsme používali jen velmi obecné metody, které jsou definované v BasePresenteru - ty ale podle vazby na další entitu filtrovat neumí
    - => v rámci TodoRepository zkusíme upravit metody ```findAllBy``` a ```findCountBy```
- na vaší volbě nechávám, zda chcete možnost filtrování podle tagu doplnit do obecného $whereArr, nebo dané funkci doplníte další parametr

:point_right:
- jedna z možných variant úpravy metody:
  ```php
  public function findAllByTagAndState($tagId = null, $completed = null, $offset = null, $limit = null){
    $query = $this->connection->select('*')->from($this->getTable());
    
    if ($tagId){
      //pokud je zadané požadované ID tagu, najdeme ho v navázané tabulce
      $query->where('todo_id IN (SELECT todo_id FROM todo_tag WHERE tag_id=?)',$tagId); //místo ? bychom tu mohli mít také %s či %i
    }
    
    if ($completed!==null){
      //pokud je zadaný požadovaný stav, budeme podle něj filtrovat
      $query->where(['completed'=>$completed]);
    }
  
    //necháme si úkoly seřadit podle stavu a deadline
    $query->orderBy('completed');
    $query->orderBy('%n IS NOT NULL','deadline');//%n označuje název sloupce
    $query->orderBy('deadline');
    
    //necháme vytvořit entity
    return $this->createEntities($query->fetchAll($offset, $limit));
  }
  ```

### Persistentní proměnné
:point_right:
- je v tom trošku "magie", ale v presenteru či komponentách můžeme mít proměnné, které chceme automaticky přidávat ve všech požadavcích
    - např. zvolený tag, podle kterého filtrujeme, chceme mít k dispozici i při návratu z editace úkolu
    - obdobně můžeme chtít předávat např. aktuálně zvolený jazyk ve vícejazyčné aplikaci
- takovouto proměnnou označíme dokumentačním komentářem:
    ```php
    class MujPresenter extends Nette\Application\UI\Presenter {
      /** @persistent */
      public int $page; //pozor, nastavení typu funguje až od PHP 7.4
    }    
    ```
- z hlediska Nette bude proměnná automaticky přidána jako parametr ke všem požadavkům
- pokud chceme hodnotu persistentní proměnné změnit, máme na výběr 2 varianty:
    - změníme její hodnotu v presenteru před vykreslením šablony (např. do ní dáme hodnotu z formuláře)
    - přidáme do makra pro tvorbu odkazu její hodnotu:
        ```latte
        <a href="{link default page=>2}">další strana</a>
        ```   
- pokud budeme chtít nastavení persistentní proměnné smazat, nastavíme jí hodnotu ```null```
- **POZOR:** nezapomeňte, že jde o data získaná od uživatele (může je podstrčit do URL) => musíme zkontrolovat, že např. daná stránka vůbec je k dispozici 

### Paginator
:point_right:
- = pomůcka pro vyřešení stránkování
- sám o sobě neřeší vykreslení v šabloně, ale je zahrnut v některých vizuálních komponentách

:point_right:
- vytvoření paginatoru v presenteru:
    ```php
    // Vyrobíme si instanci Paginatoru a nastavíme jej
    $paginator = new Nette\Utils\Paginator;
    $paginator->setItemCount($itemsCount); // celkový počet položek
    $paginator->setItemsPerPage(10); // počet položek na stránce
    $paginator->setPage($page); // číslo aktuální stránky
    
    $selectOffset = $paginator->offset;
    $selectLimit = $paginator->length;
  
    $this->template->paginator = $paginator; //paginator si předáme do šablony, abychom měli údaje pro vykreslení stránkování
    ```
- jednoduchý příklad stránkování:
    ```latte
    <div class="pagination">
        {if !$paginator->isFirst()}
            <a n:href="default page=>($paginator->page-1)">Předchozí</a>
        {/if}
    
        Stránka {$paginator->getPage()} z {$paginator->getPageCount()}
    
        {if !$paginator->isLast()}
            <a n:href="default page=>($paginator->getPage()+1)">Další</a>		
        {/if}
    </div>
    ```

### Multiplier - jedna komponenta ve stránce víckrát
:point_right:
- v některých případech narážíme na to, že chceme jeden formulář (či jinou komponentu) vykreslit ve stránce víckrát
    - např. v e-shopu chceme v přehledu položek u každé z nich pole pro přidání zvoleného počtu kusů do košíku
- místo konkrétní komponenty vrátí metoda createComponent instanci třídy Nette\Application\UI\Multiplier, která si při callbacku dovytvoří příslušný formulář

:point_right:
```php
protected function createComponentCartItemForm(): Multiplier {
	return new Multiplier(function ($itemId) {
		$form = $this->cartItemFormFactory->create();
        $form->setDefaults(['itemId'=>$itemId]);
		return $form;
	}); 
}
```
```latte
{control "cartItemForm-$item->id"} {*vykreslí konkrétní formulář, do kterého bude předána při vytvoření předán parametr $itemId s hodnotou $item->id*}
```

:blue_book:
- [návod k použití multiplieru](https://doc.nette.org/cs/3.1/cookbook/multiplier)

---

## Smazání adresáře cache
:point_right:
- pár z vás narazilo na potřebu smazat adresář *temp/cache*, ale smazání přes sftp připojení k serveru eso.vse.cz nefunguje. Problém je v uživatelských právech, neboť dané soubory byly vytvořeny z PHP, které běží na serveru pod vlastním uživatelem
    - => smazat daný obsah může zase PHP
- nahrajte do složky *www* soubor [deleteCacheDir.php](./deleteCacheDir.php) a načtěte jej přes prohlížeč       
