# 1. Úvod, potřebné základní znalosti

## Výukový server eso.vse.cz
:point_right:
- [informace k připojení](./server-eso.md)
- aktuálně je na serveru PHP ve verzi 7.3, v dohledné době plánován přechod na 7.4
- k dispozici má každý student jeden adresář pro umístění webu a 1 databázi (MariaDB) 
- [homepage serveru eso.vse.cz](https://eso.vse.cz/)

:mega:
Vyzkoušejte si připojení k serveru:
- nahrajte na server statický soubor a zobrazte jej přes prohlížeč
- vyzkoušejte připojení k databázi přes [phpMyAdmin](https://eso.vse.cz/phpmysqladmin/)

## Úvodní rozcvička
:point_right:
1. stáhněte si [základ ukázkové aplikace](./rozcvicka)
2. vytvořte si v databázi tabulku *prispevky*, do kterých se budou ukládat příspěvky vkládané na "nástěnku" a vložte do ní alespoň 3 záznamy
3. zkuste vypsat příspěvky z databáze na "nástěnku"  
4. zkuste naprogramovat odpovídající funkcionalitu tak, aby při odeslání formuláře došlo k uložení nového příspěvku do databáze (odeslaná data by bylo vhodné také zvalidovat)

## Objektové programování v PHP
:point_right:
- *Proč vlastně programovat objektově, když to v PHP není nezbytné?*
- pár základních konstruktů, které bychom měli znát:
    - třídy, abstraktní třídy, rozhraní
    - základy dědičnosti
    - jmenné prostory
    - traity
    - výjimky
    - autoload tříd
    - magické metody

:point_right:

```php
namespace App\Model;

class JmenoTridy extends NadrazenaTrida implements Rozhrani1,Rozhrani2 {
  const KONSTANTA = "hodnota"; //definice konstanty
  private string $x = 'a'; //definice private property s výchozí hodnotou
  public $y;        //veřejně dostupná property
  protected $z;     //property chráněná proti překrytí v dědičné třídě
  static $a; //statická proměnná třídy

  /**
   *  Konstruktor
   *  @param string|null $param
   */
  public function __construct(?string $param){
    parent::__construct();//zavolání rodičovského konstruktoru
    $this->y = $param;    //přiřazení hodnoty do property
    $this->mojeFunkce();  //zavolání funkce
  }

  /**
   *  Private funkce, dostupná jen z instance daného objektu
   */
  private function mojeFunkce():void {
    //tělo funkce
    self::statickaFunkce(); //pomocí self přistupujeme ke statickým proměnným a metodám
  }

  /**
   *  Ukázka statické funkce
   *  @return bool
   */
  public static function statickaFunkce():bool {
    //tělo funkce
    return true;
  }
}

$instance = new JmenoTridy("a"); //vytvoření instance
echo $instance->y; //přístup k public property
$instance->cosi = 'a'; //dynamicky definovaná property je vytvořena jako public
JmenoTridy::$a = 1; //přístup k statické proměnné třídy
JmenoTridy::statickaFunkce(); //zavolání statické metody
```

:blue_book:
- příklady k základům objektů z kurzu 4iz278 - [1. část](https://github.com/4iz278/cviceni/tree/domaci-vyuka-LS-2020/03-objekty), [2. část](https://github.com/4iz278/cviceni/tree/domaci-vyuka-LS-2020/04-objekty-II-validace)

### Zajímá nás verze PHP?
:point_right:
- postupně přibývají věci, které usnadní kontrolu a zabezpečení kódu, naopak ale také není zaručena plná zpětná kompatibilita

**Pro použití ve verzi 7.4:**
- kontrola datových typů u parametrů funkcí i properties, návratové datové typy metod
- anonymní a arrow funkce
- operátor **??**
    ```php
    $user = $_GET['user'] ?? 'nobody';
    ```
- operátor **...** pro slučování polí
    ```php
    $parts = ['apple', 'pear'];
    $fruits = ['banana', 'orange', ...$parts, 'watermelon'];
    ```
    
**Nově ve verzi 8:**
- union types
    ```php
    public function foo(Foo|Bar $input): int|float;
    ```
- nulsafe operator:
    ```php
    $dateAsString = $booking->getStartDate()?->asDateTimeString();
    ```
- pojmenované parametry funkcí a metod:
    ```php
    function foo(string $a, string $b, ?string $c = null, ?string $d = null){
       //
    }
    foo(
        b: 'value b', 
        a: 'value a', 
        d: 'value d',
    );
    ```

- atributy
    ```php
    #[ExampleAttribute]
    class Foo{
        //
    }
    ```

## Composer, Packagist
:point_right:
- Pokud chceme pracovat s externími "knihovnami" (balíčky tříd), je v PHP obvyklé neskládat dané kódy ručně, ale spracovat závislosti projektu pomocí composeru.
- Composer = správce závislostí pro PHP projekty
    - viz [https://getcomposer.org](https://getcomposer.org)
    - distribuován v podobě PHAR archívu (= ZIP archív s instrukcemi pro spuštění zahrnutých PHP skriptů), ale např. na windows si ho můžete nainstalovat také pomocí běžného instalátoru.
- Jako správce balíčků se používá Packagist, nebo GITové úložiště (nejčastěji GitHub)
    - Můžete si definovat vlastní znovupoužitelné komponenty, které jednoduše začleníte do většího množství projektů.
    - Pokud je použitá komponenta závislá na dalších komponentách, composer automaticky vyřeší a stáhne i všechny její závislosti.

### Postup použití
:point_right:
- vytvoříme soubor composer.json, ve kterém uvedeme potřebné závislosti
- následně z konzole spustíme ```composer update```, respektive ```php composer.phar update```
- kromě ručního zápisu jde composer ovládat také konzolovými příkazy, např. ```composer require mpdf/mpdf```

:blue_book:
- [příklad jednoduchého projektu s composerem](./composer)


## Vývojové prostředí
:point_right:
- vývoj lokálně vs. na serveru
- důrazně doporučuji nainstalovat si vhodné vývojové prostředí (IDE)
    - pomůže vám s psaním kódu pomocí našeptávání a s kontrolou základních chyb
    - integrován GIT
    - deploy rovnou na server
- vhodné editory:
    - [PhpStorm](https://www.jetbrains.com/phpstorm/)
    - VSCode
    - Netbeans    
- [vhodná rozšíření pro Nette](https://doc.nette.org/cs/3.1/editors-and-tools)    

## Framework Nette
- jeden z velmi populárních a stále vyvíjených PHP frameworků - *znáte nějaké další?*
- vyvíjen v ČR, silná lokální komunita
- řada příjemných funkcí usnadňujících vývoj - Latta, Tracy, tvorba odkazů, komponentový přístup k vývoji... 

:blue_book:
- https://nette.org/cs/

### Ukázková první aplikace v Nette
:point_right:
- vyzkoušíme si vytvořit první ukázkovou aplikaci, která je jakýmsi demo projektem v Nette
- inicializace projektu pomocí composeru, základní struktura aplikace, nastavení práv k adresářům

Nejprve bude nutné stáhnout composer. Na vlastním počítači jej doporučuji nainstalovat z [této stránky](https://getcomposer.org/download/), na vzdáleném serveru jde ale použít také rovnou PHAR archív spouštěný pomocí PHP.

:mega:

1. připojte se k serveru eso.vse.cz pomocí ssh
2. ve svém domovském adresáři se přepněte do ```public_html``` (reálná cesta na serveru je ```/home/httpd/users/xname```)
3. vytvoříme si složku pro první projekt
    ```shell script
    mkdir cviceni01
    cd cviceni01
    ```
4. stáhneme composer
    ```shell script
    wget https://getcomposer.org/download/2.1.8/composer.phar
    ```
5. pomocí composeru vygenerujeme ukázkovou aplikaci, se kterou budeme dále pracovat
    ```shell script
    php composer.phar create-project nette/web-project nette-demo
    ```
6. nastavíme práva k potřebným adresářům
    ```shell script
    cd nette-demo
    chmod 777 temp
    chmod 777 cache
    ```
7. zkuste aplikaci načíst přes prohlížeč - adresa by měla být [https://eso.vse.cz/~xname/cviceni01/nette-demo/www](https://eso.vse.cz/~xname/cviceni01/nette-demo/www)   