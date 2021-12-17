# 12. E-shop - jiné typy výstupu (XML, JSON, PDF), AJAX, lokalizace  

## Jiné typy výstupu
:point_right:
- ne vždy chceme generovat výstup ve formátu HTML - např. pro komunikaci s javascriptem se hodí JSON či XML, sitemapa bude v XML atd.
- výstup v jiném formátu můžeme generovat buď prostřednictvím  latte šablony, ve které uvedeme jiný content type, nebo můžeme odpověď vygenerovat v presenteru

### Odeslání JSON odpovědi rovnou z presenteru
:point_right:
- pro možnost skládání dat odpovědi máme v presenteru k dispozici property *payload*, do které můžeme postupně přidat data, která chceme odeslat
- tuto činnost bychom měli mít v "handle*" či "action*" metodě 
- samotné odeslání poté probíhá v případě detekce AJAXu automaticky, nebo jej můžeme odeslat ručně
  ```php
    public function actionJsonData():void {
      $this->payload->a = 1;
      $this->payload->b = 2;
      $this->sendPayload();
  ```

:point_right:    
- pokud chceme poslat kompletní vlastní JSON, můžeme data předat k odeslání také jako pole či objekt serializovatelný jako JSON:
  ```php
  public function actionJsonData():void {
    $this->sendJson([
      'a'=>1,
      'b'=>2
    ]);
  }
  ```

### Odeslání XML odpovědi rovnou z presenteru
:point_right:
- v rámci presenteru můžeme akci vlastně kdykoliv ukončit odesláním odpovědi, případně můžeme odpověď v rámci presenteru upravovat
- ukázka sestavení odpovědi pomocí SimpleXML:
  ```php
  public function actionXmlData():void {
    $xml = new \SimpleXMLElement('<test></test>');
    $xml->addChild('a', 1);
    $xml->addChild('b', 2);
  
    $this->getHttpResponse()->setContentType('application/xml');
    $this->sendResponse(new TextResponse($xml->asXML()));
  }
  ```

### XML pomocí latte - sitemap.xml 
:point_right:
- sitemap.xml je základní rozcestník, který má vyhledávačům napovědět, jaké stránky na našem webu najde
    - v eshopu by to měly být stránky jednotlivých produktů, kategorií... 
- sitemap můžeme jednoduše vygenerovat přímo v aplikaci - základ k dopracování je uveden v ukázkové aplikaci

:point_right:
- v latte šabloně můžeme na jejím začátku uvést, jaký typ výstupu chceme generovat; následně pak generujeme potřebné XML značky
  ```latte
  {contentType application/xml}
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
      <url>
        <loc>{$baseUrl}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
      </url>
      
      {*TODO výpis jednotlivých produktů*}
      
    </urlset>
    ```
- sitemapa by ideálně měla být v ideálním stavu *sitemap.xml* v kořenové složce webu a zároveň zapsaná v *robots.txt*
    - pro namapování na presenter/akci můžeme použít buď podsunutí v .htaccessu, nebo jednoduše přidání další cesty v routeru:
    ```php
    $frontRouter = new RouteList('Front');
    $frontRouter->addRoute('sitemap.xml', 'Homepage:sitemap');
    ```

:blue_book:
- [Sitemap XML format](https://www.sitemaps.org)
- [XML Sitemap Validator](https://www.xml-sitemaps.com/validate-xml-sitemap.html)

:mega:
- dopracujte v ukázkové aplikaci výpis sitemapy (alespoň pro produkty)  

### Odeslání datového souboru jako odpovědi
:point_right:
- jednou z variant je "klasický" postup s odesláním správných HTTP hlaviček a odesláním obsahu souboru pomocí metody ```readfile```
- v rámci presenteru máme ale také jednodušší možnost soubor odeslat pomocí speciálního typu odpovědi *FileResponse*
    - máme možnost zadat také jméno souboru (na serveru ho můžeme mít uložený např. v TEMPu pod nějakým dočasným jménem, ale chceme jej nabídnout s normální čitelnou variantou jména)
    - lze definovat content type, či případně vynutit stažení souboru (obrázku či PDF) pomocí forceDownload

```php
public function actionXmlData():void {
  //TODO 
  $this->sendResponse(new FileResponse($tempFile, 'objednavka.pdf', 'application/pdf', false));
}
```    

### Generujeme PDF
:point_right:
- pro generování PDF doporučuji knihovnu [mPDF](https://mpdf.github.io/)
    - = srozumitelná knihovna, která umožňuje generovat PDF z HTML záznamu (byť s jistým omezením např. v oblasti stylů) 
- knihovnu nejprve nainstalujeme do projektu
    ```
    composer require mpdf/mpdf
    ```    
- následně můžeme využívat instanci třídy Mpdf, které buď předáme vlastnoručně sestavený HTML kód, nebo si jej necháme vygenerovat šablonou
- pro správnou funkčnost jen musíme změnit adresář temp na složku, do které je možné zapisovat
    - důrazně doporučuji, není vhodné mít cache schovanou v adresáři *vendor*

:point_right:    
```php
public function actionPdf(){
  //připravíme instanci MPdf a nastavíme dokumentu název a tempDir
  $pdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format'=>'A4', 'tempDir'=>__DIR__.'/../../../temp/mpdf']);
  $pdf->title = 'Objednávka';

  //generujeme obsah podle konkrétní latte šablony
  $this->template->setFile(__DIR__.'/templates/Demo/pdf.latte');
  $pdf->WriteHTML($this->template->renderToString());

  //odešleme výstup
  $pdf->Output('objednavka.pdf', \Mpdf\Output\Destination::INLINE);
  $this->terminate();
}
```   

:point_right:
- pokud bychom chtěli dokument odeslat mailem:
  ```php
  public function actionSendPdf(){
    //TODO příprava PDF

    //připravíme mail, do kterého necháme přidat PDF přílohu
    $mail = new \Nette\Mail\Message();
    $mail->addAttachment('objednavka.pdf', \Mpdf\Output\Destination::STRING_RETURN);
  
    //TODO odeslání mailu
  }
  ```

---

## Lokalizace aplikace do více jazyků
:point_right:
- poměrně často chceme mít jednu aplikaci dostupnou např. zároveň v češtině a v angličtině
- i pokud to není v zadání rovnou, ale může se tento požadavek vyskytnout v budoucnu, chovejme se tak, jako bychom měli překládat aplikaci hned
    - později bychom si tím přidělali výrazně více práce, neboť bychom museli procházet celý zdrojový kód   

### Translator
:point_right:
- pro funkčnost lokalizace musíme mít v aplikaci zahrnutou třídu implementující rozhraní *Nette\Localization\Translator*, kterou přiřadíme do šablon, formulářů atp.
- v Nette žádná výchozí implementace není, ale je k dispozici několik hotových balíčků, nebo si můžeme napsat vlastní
    - viz např. [balíčky na webu Componette](https://componette.org/search/localization)
    - ukázku vlastního překladače s ukládáním řetězců do databáze [najdete tady](https://github.com/vojir/Nette-DatabaseTranslator)
- pokud si chcete lokalizaci jen připravit, ale bude se dodělávat až v budoucnu, zkuste zahrnout translator vracející řetězce bez reálného překladu:
    ```composer require vojir/nette-blank-translator```

### Přiřazení translatoru do šablon a formulářů:
:point_right:
```php
use \Nette\Localization\Translator;

abstract class BasePresenter extends \Nette\Application\UI\Presenter{ 
  /** @persistent */
  public string $lang; //doporučuji využít persistentní proměnnou pro uložení aktuálně vybraného jazyka

  private Translator $translator;
  
  public function __construct(Translator $translator){
    $this->translator=$translator;
  }

  /**
   * Metoda volaná před vykreslováním šablony
   */
  public function beforeRender(){
    $this->template->setTranslator($this->translator);
  }
}
```

### Překlady v šablonách
:point_right:
- nejjednodušší variantou je vypisovat všechny lokalizované hodnoty pomocí překladového makra:
    ```latte
    {_'překládaný řetězec'}
    {_}překládaný řetězec{/_}
    {_$promenna}
    ```
- alternativně funguje také filter *translate*, který můžeme využít např. pokud chceme hodnotu nejprve přeložit a poté upravit dalším filtrem (např. zkrátit):
    ```latte
    {$promenna|translate}
    {$promenna|translate|truncate:20}
    ```

### Překlady na dalších místech aplikace
- formuláře se umějí překládat samy - stačí jim přiřadit translator:
    ```php
    $form = new \Nette\Application\UI\Form();
    $form->setTranslator($translator);
    ```
    
- pokud bychom potřebovali překlady např. pro výstup generovaný z akce presenteru, pro maily atp.
    - využijeme na instanci translatoru přímé zavolání metody *translate*
        ```php
        echo $this->translator->translate('Lorem ipsum...');
        ```
    - pro překlad ve vlastní komponentě si necháme předat Translator jako parametr konstruktoru    

:blue_book:
- [Překládání na webu Nette](https://doc.nette.org/cs/3.1/translations)

---  

## AJAX prostřednictvím knihovny Naja
:point_right:
- v Nette najdeme podporu pro řešení ajaxového chování aplikace prostřednictvím překreslování pouze vybraných částí šablon
    - při prvním požadavku na danou stránku se přenese celá šablona
    - při dalších dílčích změnách se přenášejí jen vybrané části, které označujeme jako "snippety" 
- samotné překreslení části stránky musí vyřešit javascript, starý jednoduchý skript už ale není podporován - v současné době se používají [knihovna Naja](https://naja.js.org/) nebo [Nittro framework](https://www.nittro.org/)
    - budeme využívat knihovnu Naja, která se specializuje jen na ajaxová volání a je jednodušší 

:blue_book:
- [AJAX & snipety na webu Nette](https://doc.nette.org/cs/3.1/ajax)
- [Knihovna Naja](https://naja.js.org/)

### Snippety a jejich invalidace
:point_right:
- v rámci šablon můžeme označit bloky, které má jít překreslit samostatně
    - nemusí jít o komponenty, na snippety můžeme rozdělit i normální šablony
- základní označení snippetu:
  ```latte
  {snippet jmenoSnippetu}
      html obsah
  {/snippet}  
  
  <div n:snippet="jmenoSnippetu2">html obsah</div>
  ```    
- pokud chceme snippety vykreslit dynamicky (např. položky z foreach cyklu), obalíme je ještě jedním společným snippetem:
  ```latte 
  <ul n:snippet="seznam">
    {foreach $items as $item}
      <li n:snippet="polozka-{$item->itemId}">...</li>
    {/foreach}
  </ul>
  ``` 
  - následně pak bude nutné invalidovat i nadřazený snippet

- u vlastních komponent bývá dobrým zvykem mít celý jejich obsah vždy zabalený jako snippet, v rámci komponenty poté zavoláme na vhodném místě metodu *redrawControl()*  

:point_right:   
- v rámci presenteru poté můžeme vynutit překreslení:
  ```php
  public function handleAdd():void {
    if ($this->isAjax()){
      $this->redrawControl('seznam');
    }else{
      $this->redirect('this');
    }
  }
  ```

### Knihovna Naja
:point_right:
- označíme si ve stránce bloky, které budeme chtít samostatně překreslovat (např. košík, flash zprávy...) - reálně můžeme nechat překreslit klidně i celý hlavní obsah stránky
- knihovna se ve výchozím nastavení snaží odchytit a zpracovat všechny požadavky z odkazů a formulářů, které u sebe mají třídu *ajax*
- celou knihovnu můžeme zahrnout mezi další knihovny využívané projektem, nebo ji necháme stáhnout z CDN:
  ```html
  <script src="https://unpkg.com/naja@2/dist/Naja.min.js"></script>
  ```
- pro použití musíme knihovnu inicializovat:
  ```
    $(document).ready(function(){
        naja.initialize();
    });  
  ```
  
:point_right:
- ukázku přidáním a odebráním zboží do košíku najdete v ukázkovém eshopu  
  
---

## Ukázková aplikace

:mega:

1. stáhněte si složku **[eshop](./eshop)** se zdrojovým kódem projektu, nahrajte její obsah na server (a nezapomeňte na úpravu práv k adresářům *log* a *temp*)
2. v případě potřeby můžete využít také [export databáze](./eshop-db.sql)
3. v souboru **config/local.neon** přístupy k databázi, později také přístupy k FB loginu
4. upravte práva k adresáři *www/img/products* (nastavte práva 777)
