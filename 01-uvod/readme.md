# 1. Úvod, potřebné základní znalosti

## Výukový server eso.vse.cz
:point_right:
- [informace k připojení](./server-eso.md)
- aktuálně je na serveru PHP ve verzi 8.1
- k dispozici má každý student jeden adresář pro umístění webu a 1 databázi (MariaDB) 
- [homepage serveru eso.vse.cz](https://eso.vse.cz/)

:mega:
Vyzkoušejte si připojení k serveru:
- nahrajte na server statický soubor a zobrazte jej přes prohlížeč
- vyzkoušejte připojení k databázi přes [phpMyAdmin](https://eso.vse.cz/myadmin/)

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
    - autoload tříd, [composer](../02-zakladni-koncepty#composer-packagist)
    - magické metody

:point_right:

```php
namespace App\Model;

class JmenoTridy extends NadrazenaTrida implements Rozhrani1,Rozhrani2 {
  const KONSTANTA = "hodnota"; //definice konstanty
  private string $x = 'a'; //definice private property s výchozí hodnotou
  public $y;        //veřejně dostupná property bez datového typu
  protected $z;     //property chráněná proti překrytí v dědičné třídě
  static string $a; //statická proměnná třídy

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
- definice properties v konstruktoru třídy
    ```php
    class DemoProp {
      public function __construct(){
        public int $a = 1;
      }
    }
    ```
- atributy
    ```php
    #[ExampleAttribute]
    class Foo{
        //
    }
    ```

**Nově ve verzi 8.1:**
- výčtový typ Enum
    ```php
    enum Status{
      case Draft;
      case Published;
    }
    
    function setStatus(Status $status): void{
      //TODO
    }
    ```
- kontrola vícenásobné implementace rozhraní u parametru funkce/metody
    ```php
    function count_and_iterate(Iterator&Countable $value):int {
      foreach ($value as $val) {
        echo $val;
      }
      return count($value);
    }
    ```
- návratový typ never
    ```php
    function redirect(string $uri):never {
      header('Location: ' . $uri);
      exit();
    }

    function redirectToLoginPage(): never {
      redirect('/login');
      echo 'Hello'; // <- dead code detected by static analysis
    }
    ```
- pro serializaci objektů je potřeba využívat magické metody *__serialize* a *__unserialize*  

## Pojďme si to ověřit na kousku kódu
```php
class Test {
  private string|int $hodnota;
  public States $state;

  public function __construct(
    public int $a = 1,
    private int $b = 2,
    private int $c = 2,
  ){
    $this->hodnota=$a+$b+$c;
  }

  public function vypis():void {
    echo $this->a.' '.$this->b;
    echo $this->hodnota;
  }
}

enum States{
  case new;
  case edited;
  case submitted;
}

$test1 = new Test(b: 0);
$test1->state=States::new;

var_dump($test1);
```

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
- [vhodná rozšíření pro Nette](https://doc.nette.org/cs/best-practices/editors-and-tools)

:house:
- **připravte si na svém vlastním počítači vhodné vývojové prostředí pro programování během tohoto semestru**
