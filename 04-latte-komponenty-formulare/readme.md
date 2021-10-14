# 4. Latte, komponenty a formuláře

## Opakování
:point_right:
- Na minulém cvičení jsme se bavili o přístupech k datům v databázi pomocí Nette Database a pomocí LeanMapperu
- Co je to objektově-relační mapování?
- Jaká je úloha *modelu* v návrhovém vzoru MVP?

## Ukládání data v databázi - jmenné konvence
:point_right:
- doporučuji
    - používat pro názvy tabulek a jejich sloupců jednotnou velikost písmen (všechna malá či všechna velká)
    - slova se obvykle oddělují podtržítky (ale jde to teoreticky i bez toho)
- názvy tabulek
    - je nutné zvážit, zda chceme názvy v jednotném či množném čísle (např. *article* vs. *articles*)
        - pro ORM je častější jednotné číslo
        - z hlediska lidské čitelnosti SQL dotazů je asi vhodnější číslo množné
- pro většinu tabulek je obvyklé vytvářet umělé primární klíče
    - z hlediska jmenných konvencí zvažte, zda se bude primární klíč jmenovat např. *article_id*, nebo jen *id*
    - pojmenování s názvem tabulky je při psaní SQL na první pohled jednoznačnější, ale fungují obě varianty
        
### Mappery pro LeanMapper              
:point_right:
- pro jednodušší použití jsem pro vás připravil mappery převádějící camelCase názvy entit a jejich properties na podtržítkovou notaci v databázi
- vyberte si, zda potřebujete mapper pro tabulky pojmenované v jednotném čísle, nebo v množném
- pro začlenění do projektu můžete použít composer, nebo si [stáhněte zdroják](https://github.com/vojir/LeanMapper-Mappers) a upravte si kód dle vlastní potřeby
    ```shell script
    composer require vojir/leanmapper-mappers
    ```
- následně třídu vybraného mapperu zaregistrujete v common.neon - např.:
    ```neon
    services:
      - Vojir\LeanMapper\Mappers\CamelcaseUnderdashMapper('App\Model\Entities')
    ```  


*podklady budou doplněny před příslušným cvičením...*