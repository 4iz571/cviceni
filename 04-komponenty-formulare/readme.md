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

## Flash zprávy
:point_right:
- jednoduchý způsob, jak na webu zobrazovat potvrzovací či chybové hlášky (např. "položka byla uložena")
- lze je generovat jak z presenterů, tak také z komponent

```php
$this->flashMessage('zpráva', 'error'); //2. parametr je volitelný, používá se pro odlišení typu zprávy - např. info, warning, error
```

## Komponenty
:point_right:
- komponenta = kousek aplikace, který se "stará sám o sebe"
    - je vykreslitelný na místě, kde jej chceme použít
    - umí reagovat na vlastní události (požadavky na zobrazení, signály)
    - můžeme ho použít na různých místech v aplikaci
- příkladem může být například hlasování v anketě, menu, zobrazení přihlášeného uživatele, formulář...
- dnes si vysvětlíme jen jejich základy a poté se podrobněji podíváme na formuláře

### Vytváření a použití komponent
:point_right:
- dobrou praxí je oddělit komponenty do samostatného adresáře, nejčastěji **app/Components**
    - komponentu zabalíme do samostatného adresáře, ve kterém bude třída komponenty a její šablony
- komponenta si může vyžádat libovolné potřebné závislosti (fasády atp.)

:point_right:
- v presenteru máme definovanou metodu **createComponentJmenoKomponenty**, např.:
    ```php
    protected function createComponentDemo():DemoControl {
      return new DemoControl();
    } 
    ```
- pro vytvoření komponenty se používá rozhraní, které nám předá patřičné závislosti a komponentu vytvoří (pokud tedy komponenta nějaké závislosti má)
- v ostatních metodách presenteru (např. v renderXXX či actionXXX) získáme komponentu pomocí:
    ```php
    $demo = $this->getComponent('demo');
    $demo->text='Lorem ipsum...';
    ```

:point_right:  
- v šabloně můžeme komponentu vykreslit pomocí:
    ```latte
    {control demo} {*vykreslí komponentu demo pomocí její metody render()*}
    {control demo} {*vykreslí komponentu demo pomocí její metody render()*}
    {control demo:hello 'good morning'} {*vykreslí komponentu demo pomocí její metody renderHello s předáním parametru*}
    {*pozor, při předání parametrů jako pole najdeme v render metodě toto pole celé v 1. parametru! (rozdíl oproti presenterům)*}
    ```
  
:blue_book:
- [ukázkový příklad DemoControl](./DemoControl)
- [Komponenty na webu Nette](https://doc.nette.org/cs/3.1/components)  

## Formuláře
:point_right:  
- určitě už jste se dost natrápili s psaním kontrol k formulářům a jejich zpracováním, ale v Nette je formulář prostě jedním z druhů komponent
- formulář seskládáme v PHP, přičemž k jednotlivým prvkům (vstupním polím, tlačítkům atp.) definujeme jejich vlastnosti a HTML formulář s kontrolami se z toho seskládá sám
- formulář už jsme používali i na minulé hodině (tady), ale dnes se na ně podíváme podrobněji
- když načteme do stránky patřičný javascriptový soubor, fungují kontroly jak v javascriptu, tak v PHP

:blue_book:
- [Formuláře na webu Nette](https://doc.nette.org/cs/3.1/forms)

### Vytvoření jednoduchého formuláře
:point_right:
1. vytvoříme instanci třídy **Nette\Application\UI\Form**
2. přidáme jednotlivá pole, tlačítka atp.
    - můžeme rozhodnout, zda je prvek povinný, nebo volitelný (pomocí **setRequired**)
    - pomocí **addRule** přidáváme validační pravidla 
    - pomocí **addFilter** můžeme doplnit ošetření vstupu
3. přidáme reakci na odesílací tlačítka

:blue_book:
- [Formulářové prvky](https://doc.nette.org/cs/3.1/form-controls)
- [Validace](https://doc.nette.org/cs/3.1/form-validation)

:point_right:
```php
use Nette\Application\UI\Form;

$form=new Form();

$form->addText('name','Jméno:')
  ->setRequired('Vyplňte jméno!');

$form->addEmail('email','E-mail:')
  ->setRequired('Vyplňte e-mail!')
  ->addFilter(function($value){
    return mb_strtolower($value);
  });

$form->addInteger('age','Věk')
  ->addRule(Form::RANGE, 'Věk musí být v rozmezí od %d do %d.', [15, 40]);

$password=$form->addPassword('password', 'Heslo:');
$password
  ->setRequired('Musíte vyplnit heslo!')
  // pokud není heslo delší než 8 znaků, musí obsahovat číslici
  ->addCondition($form::MAX_LENGTH, 8)
    ->addRule($form::PATTERN, 'Musí obsahovat číslici', '.*[0-9].*');;

$form->addPassword('password2', 'Heslo znovu:')
  ->addRule(Form::EQUAL, 'Hesla se neshodují!', $password);

$form->addSubmit('submit','odeslat')
  ->onClick[]=function(SubmitButton $submitButton){
    $values = $submitButton->form->getValues('array'); //vrací ošetřené hodnoty
    //TODO
  };

$form->addSubmit('cancel','zrušit')
  ->setValidationScope([])//zrušíme validace
  ->onClick[]=function(SubmitButton $submitButton){
    //TODO
  };
```

### Získání dat z formuláře, nastavení výchozích hodnot
:point_right:
- pro získání dat máme k dispozici metody ```getValues()``` a ```getUnsafeValues()```
    - návratovou hodnotou může být pole či objekt (ArrayHash, nebo instance námi definované třídy)   
- pro zadání výchozích dat (např. když chceme editovat záznam, který už v databázi je) použijeme metodu ```setDefaults()``` 

```php
$valuesArr = $form->getValues('array');


class RegistrationFormData{
  public string $name;
  public string $email;
  public int $age;
  public string $password;
  public string $password2; 
}
$values = $form->getValues(RegistrationFormData::class);
```

### Vykreslení formuláře v šabloně
:point_right:
- je fajn nechat vykreslení na automatice (můžeme použít také jiný renderer, např. pro bootstrap)
    ```{control registrationForm}```
- volitelně ale můžeme vykreslovat formulář také zcela ručně pomocí latte maker

### Formuláře jako samostatné komponenty
:point_right:
- z třídy Form odvodíme vlastní třídu, volitelně bychom případně mohli formulář vložit jako vnitřní komponentu do té námi vytvořené
- v praxi se mi osvědčilo, aby se o uložení dat atp. postarala komponenta formuláře, následně pak v presenteru již doplníme jen zobrazení hlášek a přesměrování
- šablonu takovéto komponenty neřešíme, formulář se umí sám vykreslit 
- pro vytvoření komponenty i se závislostmi využijeme možnost definovat jen interface, tovární třídu nám Nette vygeneruje samo

## Ukázkový příklad todolist
:mega:
1. stáhněte si **[SQL soubor](./todolist-db.sql)** s exportem databáze a naimportujte jeho obsah do MariaDB (nebojte, s předchozím příkladem nijak nekoliduje)
2. stáhněte si složku [todolis](./todolist) s ukázkovým projektem, nahrajte jej na server (nezapomeňte na úpravu práv k adresářům log a temp)
3. otevřete si ukázkové projekty ve vývojovém prostředí
4. v souboru **config/local.neon** přístupy k databázi

### Úkoly pro procvičení
:mega:
1. podívejte se na strukturu databáze a doplňte do projektu entitu **TodoItem**, doplňte příslušný repozitář a chybějící metody do **TodosFacade**
2. akce Todo:default by měla vypisovat seznam všech úkolů se znázorněním jejich stavu a tagů
3. vytvořte formulář pro vytvoření/úpravu úkolu (entity **Todo**)
4. vytvořte formulář pro vytvoření podúkolu (**TodoItem**)
5. úkoly by mělo být možné označit jako hotové 