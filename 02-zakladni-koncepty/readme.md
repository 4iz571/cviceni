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