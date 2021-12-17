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









### Ukázková aplikace

:mega:

Pro dokončení košíku vyjdeme z aplikace z minulého cvičení, do které bylo doplněno pár kousků kódu. Doporučuji si ji stáhnout z GITu:
1. stáhněte si soubor s [rozdílovým exportem databáze](./eshop-diff-db-kosik2.sql) a naimportujte jeho obsah do MariaDB (případně je k dispozici také soubor s [kompletním exportem databáze](./eshop-db.sql))
2. stáhněte si složku **[eshop](./eshop)** se zdrojovým kódem projektu, nahrajte její obsah na server (a nezapomeňte na úpravu práv k adresářům *log* a *temp*)
3. v souboru **config/local.neon** přístupy k databázi, později také přístupy k FB loginu
4. upravte práva k adresáři *www/img/products* (nastavte práva 777)