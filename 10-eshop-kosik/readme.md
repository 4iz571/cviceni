# 10. E-shop - editace produktů, košík  

## Editace produktů v administraci e-shopu
:point_right:
- v rámci editace produktů v administraci e-shopu budeme muset vyřešit 2 dílčí problémy:
    1. uložení (a případné vygenerování) unikátní URL produktu - protože budeme chtít hezké webové adresy
    2. uložení fotky produktu
- v rámci ukázkového řešení se podíváme na základní variantu řešení, kterou si následně můžete přizpůsobit své vlastní aplikaci

### Unikání URL produktu
:point_right:
- u každého z produktů si uložíme kousek URL, kterou poté budeme používat pro tvorbu SEO adres (protože odkazovat se na ID produktu není úplně ideální) 
    - aby byla daná hodnota použitelná pro určení produktu, definujeme daný sloupec v databázi s klíčem unique
    - nenahrazujeme tím ID úplně, číselnou hodnotu budeme používat např. v administraci a bude "stálejší" než uživatelem upravitelná URL 
- při ukládání produktu budeme muset ověřit, zda už daná hodnota URL v databázi existuje
    - pokud jde o vytvoření nového produktu, nesmí být daná hodnota obsazena
    - pokud jde o úpravu existujícího produktu, může se hodnota vyskytnout u toho produktu, který právě upravujeme (ale u žádného jiného)
- nebudeme nutit uživatele zadávat URL ručně, ale pokud ji vyplní, budeme kontrolovat unikátnost
    - pokud hodnota vyplněna nebude, vygenerujeme ji z názvu produktu (tj. vlastně stejně, jako to dělají např. CMS při ukládání článků)      

:orange_book:
- podrobně je implementace popsaná v [komentované prezentaci](./eshop-editace-produktu.pptx)     

### Ukládání fotek produktů
:point_right:
- soubory s fotkami rozhodně nebudeme ukládat do databáze - uložíme je do zvoleného veřejně dostupné složky - tj. např. *www/img/products*  
    - nemá smysl složku jakkoliv zabezpečovat - obrázky produktů přeci chceme nabídnout i Googlu atp.
- pro nahrávání obrázků použijeme ve formuláři ```Nette\Forms\Controls\UploadControl```, na který můžeme navěšet potřebné kontroly a zpřístupní nám i samotný nahraný soubor
- pro uložení fotky zjistíme jen příponu souboru, samotný soubor pak uložíme pod názvem odpovídajícím ID daného produktu
    - tím si nebude uživatel muset hlídat případné shody jmen souborů :)
    
:point_right:

```php
$form = new \Nette\Application\UI\Form();
$photoUpload = $form->addUpload('photo','Fotka:');
//volitelná kontrola nahrání odpovídajícího obrázku
$photoUpload->addRule(function(Nette\Forms\Controls\UploadControl $photoUpload){
  $uploadedFile = $photoUpload->value;
  if ($uploadedFile instanceof Nette\Http\FileUpload){
    $extension=strtolower($uploadedFile->getImageFileExtension());
    return in_array($extension,['jpg','jpeg','png']);
  }
  return false;
},'Je nutné nahrát obrázek ve formátu JPEG či PNG.');
```

```php
if ($photoUpload->isOk()){
  //soubor je nahraný
  /** @var Nette\Http\FileUpload $uploadedFile */
  $uploadedFile = $photoUpload->value;
  if ($uploadedFile->isOk() && $uploadedFile->isImage()){
  
    $originalFileName = $uploadedFile->getUntrustedName();
    $tempFileName = $uploadedFile->temporaryFile;  

    //volitelný vlastní kód pracující se souborem - např. $uploadedFile->move($targetFileName);

  }
}
```

### Ukázková aplikace

:mega:

Implementace popsaná v komentované prezentaci s postupem je již zahrnuta do ukázkové aplikace. Pro její spuštění:
1. stáhněte si soubor s [rozdílovým exportem databáze](./eshop-diff-db-produkty.sql) a naimportujte jeho obsah do MariaDB
    - rozdílový export je vytvářen vůči databázi z minulého cvičení ([zde](../eshop-db.sql))
2. stáhněte si složku **[eshop](./eshop)** se zdrojovým kódem projektu, nahrajte její obsah na server (a nezapomeňte na úpravu práv k adresářům *log* a *temp*)
3. v souboru **config/local.neon** přístupy k databázi, později také přístupy k FB loginu
4. upravte práva k adresáči *www/img/products* (nastavte práva 777)

:mega: :orange_book:

5. projděte si [prezentaci s postupem implementace editace produktů](eshop-editace-produktu.pptx)
6. inspirujte se daným kódem a implementujte správu produktů do eshopu, který vytváříte jako semestrální práci


## Košík
:point_right:
- informace o položkách v košíku můžeme uchovávat buď v session, nebo v databázi
    - uchovávání v databázi je o trochu náročnější, ale uživatel to ocení, když si něco přidá do košíku např. na mobilu a pak chce objednávku dokončit z počítače
    - pro identifikaci košíku můžeme u přihlášeného uživatele použít jeho id, u nepřihlášeného si pak uložíme do session (či cookie) id košíku

### Práce se session
:point_right:
- pro přístup k session nebudeme používat globální proměnnou $_SESSION, ale přes obalovací objekt *Nette\Http\Session*
    - jednoduše budeme moct pracovat jen se zvolenou částí dat, případně budeme moct zvolit místo uložení dat atp.
    - svoji platnost bude mít nejen celá session, ale také jednotlivé proměnné
- objekt Session si necháme do komponenty předat jako závislost, v presenteru můžeme použít ```$this->getSession('sectionName')```

:point_right:
```php
/** @var Nette\Http\SessionSection $sessionSection */
$sessionSection = $session->getSection('sectionName');

// zápis proměnné
$sessionSection->set('promenna', 'hodnota');

// čtení proměnné, vrátí null pokud neexistuje
echo $sessionSection->get('promenna');

// zrušení proměnné
$sessionSection->remove('promenna');
```

:blue_book:
- [Sessions na webu Nette](https://doc.nette.org/cs/3.1/sessions)

### Jeden formulář chci ve stránce vícekrát - multiplier
:point_right:
- např. v e-shopu chceme v přehledu položek u každé z nich pole pro přidání zvoleného počtu kusů do košíku
- pro implementaci využijeme Multiplier, o kterém jsme se zmiňovali již na [5. cvičení](../05-latte-formulare#multiplier---jedna-komponenta-ve-str%C3%A1nce-v%C3%ADckr%C3%A1t) 
- místo konkrétní komponenty vrátí metoda createComponent instanci třídy Nette\Application\UI\Multiplier, která si při callbacku dovytvoří příslušný formulář

:point_right:
```php
protected function createComponentProductCartForm(): Multiplier {
	return new Multiplier(function ($productId) {
		$form = $this->productCartFormFactory->create();
        $form->setDefaults(['productId'=>$productId]);
		return $form;
	}); 
}
```
```latte
{control "productCartForm-$product->productId"} {*vykreslí konkrétní formulář, do kterého bude předána při vytvoření předán parametr $productId s hodnotou $product->productId*}
```

:blue_book:
- [návod k použití multiplieru](https://doc.nette.org/cs/3.1/cookbook/multiplier)

### Košík jako komponenta
:point_right:
- v rámci aplikace si můžeme celou správu košíku oddělit do komponenty, která zajistí
    - načtení košíku
    - aktualizaci jeho obsahu
    - vykreslení informace o poštu položek a ceně (pro hlavičku e-shopu) i celou stránku s přehledem košíku
- komponentu budeme potřebovat ve všech presenterech frontendové části aplikace => vytvoříme si ji v BasePresenteru        

:point_right:
- v ukázkové aplikaci je komponenta vytvořena pod názvem [CartControl](./eshop/app/FrontModule/Components/CartControl)
- pro přidávání zboží do košíku budeme používat [ProductCartForm](./eshop/app/FrontModule/Components/ProductCartForm)

:mega:
- pojďme navrhnout vhodnou strukturu pro uložení košíku v databázi a dopsat potřebný kód


## Hezké adresy pro zobrazení produktů
:point_right:
- jak již víme z předchozího cvičení, tvar URL můžeme jednoduše upravit v rámci routeru
    - nezapomeňte na to, že specifičtější varianty cest se zadávají před těmi obecnými
- vytvoříme vlastní routu pro adresování produktů, ve které použijeme místo ID produktu hodnotu jeho property URL

```php
$frontRouter = new RouteList('Front');
$frontRouter->addRoute('produkty', 'Product:list');
$frontRouter->addRoute('produkty/<url>', 'Product:show');
``` 

```php
public function renderShow(string $url):void {
  try{
    $product = $this->productsFacade->getProductByUrl($url);
  }catch (\Exception $e){
    throw new BadRequestException('Produkt nebyl nalezen.');
  }

  $this->template->product = $product;
}
```

:point_right:
- zkusme do cesty zamíchat také kategorie:

```php
$frontRouter->addRoute('produkty[/kategorie-<category>]', 'Product:list');  //pokud je do adresy zakomponována také proměnná category, je doplněna do adresy
$frontRouter->addRoute('produkty[/kategorie-<category>]/<url>', 'Product:show');  //pokud je do adresy zakomponována také proměnná category, je doplněna prostřední část adresy
```

:point_right:
- kategorii si případně můžeme v routeru načíst z databáze:

```php
public static function createRouter(ProductsFacade $productsFacade):RouteList {
  $frontRouter = new RouteList('Front');
  $frontRouter->addRoute('produkty[/kategorie-<category>]', 'Product:list');
  $frontRouter->addRoute('produkty[/kategorie-<category>]/<url>', [
    'presenter'=>'Product',
    'action'=>'show',
    null =>[
      Nette\Routing\Route::FILTER_OUT => function(array $params)use($productsFacade){
        if (!$params['category']){
          $product = $productsFacade->getProductByUrl($params['url']);
          if (!empty($product->category)){
            $params['category']=$product->category->categoryId;
          }
        }
        return $params;
      }
    ]
  ]);
  return $frontRouter;
}  
```

:blue_book:
- [Routování - návod na webu Nette](https://doc.nette.org/cs/3.1/routing)