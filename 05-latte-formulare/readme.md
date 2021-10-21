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
- podle zvoleného vývojového prostředí doporučuji nainstalovat odpovídající pluginy ([návod zde](https://latte.nette.org/cs/integrations))
- volitelně se podívejte na možnosti [definice datových typů pro šablony](https://latte.nette.org/cs/type-system)
    - na cvičeních si vystačíme s ```{varType string $text}```


## Todolist - procvičení práce s formuláři


3. vytvořte formulář pro vytvoření/úpravu úkolu (entity **Todo**)
4. vytvořte formulář pro vytvoření podúkolu (**TodoItem**)
5. úkoly by mělo být možné označit jako hotové