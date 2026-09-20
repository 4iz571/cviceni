# 1. Úvod, potřebné základní znalosti

## Výukový server eso.vse.cz
:point_right:
- [informace k připojení](./server-eso.md)
- aktuálně je na serveru PHP ve verzi 8.3
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
      public function __construct(
        public int $a = 1;
      ){        
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
- match jako alternativa ke switch
    ```php
    $message = match ($status) {
    200 => 'OK',
    404 => 'Not found',
    default => 'Unknown status',
    };
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
- readonly properties
    ```php
    class User {
      public function __construct(
        public readonly int $id,
      ) {}
    }
    ```
- pro serializaci objektů je potřeba využívat magické metody *__serialize* a *__unserialize*  

**Nově ve verzi 8.2:**
- readonly lze označit celou třídu
    ```php
    readonly class User {
      public function __construct(
        public int $id,
        public string $name,
      ){}
    }
    ```
- lze kombinovat union a intersection types (DNF types)
    ```php
    function foo((A&B)|C $value): void {
      //...
    }
    ```
- null, false a true lze používat jako samostatné datové typy
    ```php
    function alwaysTrue(): true {
      return true;
    }
    ```
- konstanty lze definovat také v traitech
    ```php
    trait ExampleTrait {
      public const TYPE = 'example';
    }
    ```
- atribut `#[SensitiveParameter]` umožňuje skrýt citlivé parametry například ve stack trace
    ```php
    function login(
      string $username,
      #[SensitiveParameter] string $password,
    ): void {
      //...
    }
    ```
- *vytváření dynamických properties je deprecated*
    ```php
    class User {
      public string $name;
    }
    
    $user = new User();
    $user->email = 'a@example.com'; //deprecated od PHP 8.2
    ``` 

**Nově ve verzi 8.3:**
- typované konstanty tříd
    ```php
    class Configuration {
      public const string ENVIRONMENT = 'production';
      public const int MAX_ITEMS = 100;
    }
    ```
- atribut `#[Override]` umožňuje ověřit, že metoda skutečně přepisuje metodu rodiče nebo implementuje metodu rozhraní. Pokud odpovídající metoda v rodičovské třídě neexistuje, PHP vyvolá chybu.
    ```php
    class Child extends ParentClass {
      #[Override]
      public function save(): void {
        //...
      }
    }
    ```
- funkce ```json_validate()``` umožňuje ověřit syntaktickou správnost JSON bez jeho dekódování

**Nově ve verzi 8.4:**
- property hooks umožňují definovat chování při čtení a zápisu property
    ```php
    class Person {
      public string $firstName {
        set => ucfirst(strtolower($value));
      } 
      public string $lastName; 
      public string $fullName {
        get => $this->firstName.' '.$this->lastName;
      }
    }
    
    $person = new Person();
    $person->firstName = 'JAN';
    $person->lastName = 'Novák';
    
    echo $person->fullName; //Jan Novák
    ``` 
- asymetrická viditelnost properties – lze samostatně určit oprávnění pro čtení a zápis
    ```php
    class User {
      public private(set) string $name;
    
      public function __construct(string $name) {
        $this->name = $name;
      }
    }
    
    $user = new User('Jan');
    
    echo $user->name;       //OK
    $user->name = 'Petr';   //chyba
    ```
- atribut `#[Deprecated]` umožňuje označit vlastní funkce a metody jako zastaralé
    ```php
    #[Deprecated('Použijte newFunction()')]
    function oldFunction(): void {
    //...
    }
    ```
- *implicitně nullable parametry jsou deprecated:*
    ```php
    //deprecated
    function foo(string $value = null): void {
    }
    
    //správně
    function foo(?string $value = null): void {
    }
    ```
  
**Nově ve verzi 8.5**
- *pipe operator* ```|>``` umožňuje předávat výsledek jedné operace do následující a zapisovat transformace zleva doprava
    ```php
    $result = ' Hello World '
      |> trim(...)
      |> strtolower(...)
      |> strlen(...);

    echo $result; //11

    //Místo například:
    $result = strlen(strtolower(trim(' Hello World ')));
    ```
- ```clone()``` umožňuje při klonování zároveň změnit vybrané properties, což je užitečné zejména pro immutable/readonly objekty.
    ```php
    readonly class User {
      public function __construct(
        public int $id,
        public string $name,
      ){}
    }
    
    $user1 = new User(1, 'Jan');
    $user2 = clone($user1, [
      'name' => 'Petr',
    ]);
    ```
- atribut `#[NoDiscard]` umožňuje označit návratovou hodnotu funkce jako hodnotu, která by neměla být ignorována
    ```php
    #[NoDiscard]
    function calculate(): int {
      return 42;
    }
  
    calculate(); //warning – návratová hodnota nebyla použita
    $result = calculate(); //OK
    ```
    
  - Pokud chceme výsledek záměrně ignorovat, lze to explicitně uvést:
      ```php
      (void) calculate();
      ```
- `#[Override]` lze nově použít také pro properties
- statické properties podporují asymetrickou viditelnost
- nové funkce ```array_first()``` a ```array_last()``` usnadňují získání první a poslední hodnoty pole

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
