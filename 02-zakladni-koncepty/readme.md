# 2. Základní koncepty aplikací v Nette

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

## Framework Nette
:point_right:
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

1. připojte se k serveru esotemp.vse.cz pomocí ssh
2. ve svém domovském adresáři se přepněte do ```public_html``` (reálná cesta na serveru je ```/home/httpd/users/xname```)
3. vytvoříme si složku pro první projekt
    ```shell script
    mkdir cviceni02
    cd cviceni02
    ```
4. stáhneme composer
    ```shell script
    wget https://getcomposer.org/download/2.4.2/composer.phar
    ```
5. pomocí composeru vygenerujeme ukázkovou aplikaci, se kterou budeme dále pracovat
    ```shell script
    php composer.phar create-project nette/web-project nette-demo
    ```
6. nastavíme práva k potřebným adresářům
    ```shell script
    cd nette-demo
    chmod 777 temp
    chmod 777 log
    ```
7. zkuste aplikaci načíst přes prohlížeč - adresa by měla být [https://esotemp.vse.cz/~xname/cviceni02/nette-demo/www](https://esotemp.vse.cz/~xname/cviceni02/nette-demo/www)

## Základní principy funkčnosti aplikace
:point_right:
- probírané principy si budeme ukazovat na frameworku Nette, ale řada z nich platí i pro další objektově psané aplikace (ať již na jiném frameworku, nebo i např. na vlastní implementaci MVC)
- je vhodné dodržovat **návrhové vzory** - co to je?

### Návrhový vzor MVP
- Model-View-Presenter je jedním z návrhových vzorů ze struktury MVC
    - ale MVC už znáte, ne? :)
- důvodem rozdělení je zlepšení přehlednosti aplikace a také možnost nahradit v případě potřeby jen část aplikace (např. při změně místa uložení dat budeme měnit jen model)
- jednotlivé části mohou mít i více vrstev (např. model s objektově-relačním mapováním)     

### Dependency injection
:point_right:
- každá část aplikace (presenter, komponenta atp.) má definované závislosti, které potřebuje pro svoji činnost
- závislosti předávány v konstruktoru, nebo v případě presenterů např. pomocí *inject...* method
- komponenta tedy neříká, odkud chce své závislosti získat, ale jsou jí předány při jejím vytvoření
    - předávání lze realizovat nejen ručně, ale také automaticky 
    - framework zvládá automaticky doplňovat závislosti, které jsou zaregistrované jako *služby* v konfiguraci
    
- tj. například presenter bude mít jako závislosti definované třídy z modelu, ale také factory třídy komponent, které používá (např. formulářů)

:blue_book:
- [Dependency Injection na webu Nette](https://doc.nette.org/cs/3.0/dependency-injection)    
    
### Průchod aplikací
:point_right:
- jeden hlavní "vstupní" soubor aplikace - *index.php* či *bootstrap.php*
- postupný průchod aplikací:
    1. podle cesty je určeno, jakou část aplikace chce uživatel načíst
    2. je načten příslušný presenter, kterému jsou předány požadované závislosti
    3. výběr konkrétní akce (signál, akce, vykreslení šablony)
    4. odeslání výstupu
- pozor na to, že narozdíl např. od javy či C# nejsou žádné třídy trvale v paměti, ale aplikace je vždy ve výchozím stavu (ale session atp. známe...)

:point_right:
- ve skutečnosti je průchod aplikací ještě trochu komplikovanější - kam byste zařadili např. kontrolu uživatelských práv?    
   
### Adresářová struktura projektu
:point_right:
Základní adresářová struktura:
- *app* - tady najdeme celý "výkonný" kód aplikace
    - *Presenters*
        - *templates*
    - *Model*
    - *Router* - definice "cest" v aplikaci
    - bootstrap.php - vstupní soubor aplikace, který iniciuje aplikaci a spustí vhodný presenter
- *config* - tady najdeme neon soubory s konfigurací
- *log* - výpisy chyb a další logy
- *temp* - adresář pro dočasné soubory, např. v podadresáři *cache*
- *vendor* - složka vygenerovaná composerem
- *www* - jediná složka, která by měla být volně dostupná přes web; *index.php*, styly, js...  

:point_right:
Složitější aplikace lze dále dělit do modulů - např.:
- *app*
    - *FrontModule*
        - *Presenters*, *Router*....
    - *RestModule*     

:mega:
- podívejme se na ukázkovou Nette aplikaci a její adresářovou strukturu
    
#### Ale složku www nemohu nastavit jako webroot...
:point_right:
- pokud nemáte na hostingu možnost změnit výchozí adresář dostupný přes web, lze celou aplikaci nahrát do "veřejné" složky a pomoct si přesměrováním
- potřebný *.htaccess*:
  ```apacheconfig
  RewriteEngine On
  RewriteRule ^$ www/ [L,QSA]
  RewriteRule (.*) www/$1 [L,QSA]
  ```

### Konfigurace aplikace
:point_right:
- pro konfiguraci aplikace jsou využívány soubory *.neon*
    - jde o jednoduchý strukturovaný konfigurační soubor, ve kterém jsou vnořené sekce víc "odskočené" od levého okraje
    - k odskakování lze používat buď tabulátory, nebo mezery (nelze je ale kombinovat)
- běžně máme v aplikaci minimálně 2 konfigurační soubory
    - *config.neon* (případně např. *common.neon*) - výchozí konfigurace aplikace (definice služeb, chování aplikace atp.)
    - *config.local.neon* (případně např. *local.neon*) - nastavení závislé na daném hostingu - např. údaje pro připojení k databázi

:point_right:
- sekce, které v konfiguraci obvykle najdeme:
    ```neon
    parameters:
    
    application: 
        errorPresenter: Error
        mapping:
            *: App\*Module\Presenters\*Presenter
    
    session:
        expiration: 14 days
        autoStart: yes
    
    extensions:
    
    services:
        - App\Router\RouterFactory::createRouter
    ```

:blue_book:          
- [Manuál ke konfiguraci na webu Nette](https://doc.nette.org/cs/configuring)        

## Presentery
:point_right:
- = jednotlivé "logické" celky aplikace - např. *UserPresenter*, *ArticlePresenter* atp.
- vhodný presenter i jeho "akce" jsou určeny podle URL, kterou chce uživatel načíst
- je nutné zvážit, jak vhodně rozdělit aplikaci na jednotlivé dílčí celky - nemíchejme např. přihlášení uživatele do presenteru s výpisem košíku v e-shopu
- akce vyrábíme jen pro samostatné "stránky" - ne pro ošetření toho, že uživatel např. odeslal formulář (k tomu se dostaneme příště)

:point_right:
Postupně jsou v presenteru hledány metody:
1. konstruktor, *inject* methody
2. startup
3. metody *actionXXX* (např. *actionLogout*)
    - = metody provádějící nějakou aktivní činnost, mohou končit přesměrováním, odesláním jiného typu výstupu atp.
4. metody signálů (subrequestů) začínající na *handle*
    - signálem může být např. odeslání lajku na stránce s přehledem příspěvků
5. metody *beforeRender* a *renderXXX* 
    - => vykreslení šablony
    - do presenteru se píší jen v situaci, kdy potřebujeme do šablony předat nějaké proměnné (jinak stačí definovat danou šablonu)
6. metoda *shutdown* - pokud bychom ji případně potřebovali
    
```php
public function renderHello(string $text=''): void {
	$this->template->text = $text;
}

public function actionData(): void {
	$data = ['hello' => 'nette'];
	$this->sendJson($data);
}
```    

:blue_book:
- [Manuál k presenterům na webu Nette](https://doc.nette.org/cs/3.1/presenters)

### Šablony
:point_right:
- pro vykreslování frontendu je využíván šablonovací systém Latte
- z hlediska logiky šablon je potřeba rozlišit
    - základní rozložení aplikace, které definujeme v *@layout.latte*
    - konkrétní šablony pro jednotlivé akce, obvykle umístěné v adresáři pojmenovaném podle názvu presenteru
- v rámci šablon definujeme jednotlivé *bloky*, ze kterých se stránka skládá - bloky se mohou také opakovat, překrývat atp.
- logika je podobná PHP, systému Smarty atp., ale nabízí něco navíc...

:point_right:
- pozor na to, že šablony jsou kešované - v debug režimu nám to nevadí, ale při přepnutí do produkčního je nutné vymazat adresář *tmp/cache*

:point_right:
```latte
<!DOCTYPE html>
<html>
    <head>
        <title>{$title|upper}</title>
    </head>
    <body>
        {if count($menu) > 1}
            <ul class="menu">
                {foreach $menu as $item}
                <li><a href="{$item->href}">{$item->caption}</a></li>
                {/foreach}
            </ul>
        {/if}
    </body>
</html>
```

:point_right:

Ukázka použití bloků:

```latte
<!DOCTYPE html>
<html lang="cs">
<head>
	<title>{block title}{/block}</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<div id="content">
		{block content}{/block}
	</div>
	<div id="footer">
		{block footer}&copy; Copyright...{/block}
	</div>
</body>
</html>

```

```latte
{block content}
    <h1 n:block="title">Hello world</h1>
	<p>Lorem ipsum...</p>
{/block}
```

:blue_book:
- [Web Latte](https://latte.nette.org/)

### Vytváření odkazů
:point_right:
- vlastně nikdy nejsme nuceni ručně sestavovat URL adresy pro zasílání požadavků
- jednoduše voláme akce na konkrétních presenterech (nebo komponentách) - cesty sestaví router
- ukázka vytvoření odkazu v šabloně:
    ```latte
    <a n:href="Product:show id=>$id">detail produktu</a>
    <a href="{plink Product:show id=>$id}">detail produktu</a>
    <a href="{link vote! id=>$id}">hlasovat</a>
    ```
- ukázka vytvoření odkazu v presenteru
    ```php
    $link = $this->link('Product:show',['id'=>$id]);  
    ```
- pokud chceme spustit akci na stejném presenteru, ve kterém aktuálně jsme, stačí napsat název akce
- uvedením ```//``` na začátku jména presenteru lze vynutit vygenerování absolutní adresy
- akce končící ```!``` jsou odkazem na signál  
  
### Akce presenteru nemusí končit vykreslením šablony  
:point_right:
```php
$this->error($message, $httpCode); //vygenerování chyby (alternativně to jde také vyhozením výjimky)
$this->terminate(); //ukončení bez odpovědi
$this->forward('Presenter:akce',['parametr'=>'hodnota']);//přeskočí na jinou akci, 
$this->redirect('Presenter:akce',['parametr'=>'hodnota']);//běžné HTTP přesměrování
$this->redirectPermanent('Presenter:akce',['parametr'=>'hodnota']);
$this->redirectUrl('https://adresa'); //přesměrování na jinou, externí adresu
```

## Tracy
:point_right:
- debugovací nástroj, který nejen ukládá chybové hlášky do logu, ale také je zobrazuje ve výrazně obohacené podobě
- kromě výpisů v případě chyb ji budeme používat k dumpu proměnných a k ladění cest
- v Nette aplikaci je obvykle frontendová část Tracy zapnutá ve vývojářském módu (naopak pozor, v tomto režimu nevidíme normální chybové hlášky, které uvidí uživatel)
    - poznáme ji podle zapnuté "lišty" v pravém dolním rohu aplikace
- v běžném provozu aplikace jsou logy ukládány do složky *log*

:blue_book:
- [Web Tracy](https://tracy.nette.org/cs/)

## Praktické vyzkoušení
:mega:

Představili jsme si spoustu nových věcí, pojďme si je nyní tedy vyzkoušet na testovací aplikaci
1. vyzkoušejte si do aplikace doplnit nový presenter a alespoň 2 nové akce a šablony
2. zkuste v aplikaci vygenerovat chybu 404
3. zkuste v kódu šablony či v presenteru udělat chybu a i s ní zkuste aplikaci spustit
