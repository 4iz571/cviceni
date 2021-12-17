# 11. E-shop - dokončení košíku, objednávka  

## Košík
:point_right:
- z minulého cvičení máme aplikaci s připraveným základem komponenty košíku, který se vytvoří pro anonymního i přihlášeného uživatele a ukládá se do databáze

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

### Přidáme položku do košíku - načtěme navázané entity znovu z DB

```php
/**
 * Class Cart
 * @package App\Model\Entities
 * @property int $cartId
 * @property CartItem[] $items m:belongsToMany
 */
class Cart extends Entity{

  public function updateCartItems(){
    $this->row->cleanReferencingRowsCache('cart_item'); //smažeme cache, aby se položky v košíku znovu načetly z DB bez nutnosti načtení celého košíku
  }

}
``` 

### Chceme seřadit položky v košíku

```latte
{varType App\Model\Entities\Cart $cart}
{var $cartItems = ($cart->items|sort:(function($a, $b){return strcmp($a->product->title, $b->product->title);}))}
{foreach $cartItems as $cartItem}
    {$cartItem->product->title}
{/foreach}
```

### Potřebujeme odmazávat staré košíky

```php
class CartRepository extends BaseRepository{

  public function deleteOldCarts(){
    $this->connection->nativeQuery('DELETE FROM `cart` WHERE (user_id IS NULL AND last_modified < (NOW() - INTERVAL 30 DAY)) OR (last_modified < (NOW() - INTERVAL 3 DAY))');
  }

}
```

### Ukázková aplikace

:mega:

Pro dokončení košíku vyjdeme z aplikace z minulého cvičení, do které bylo doplněno pár kousků kódu. Doporučuji si ji stáhnout z GITu:
1. stáhněte si soubor s [rozdílovým exportem databáze](./eshop-diff-db-kosik2.sql) a naimportujte jeho obsah do MariaDB (případně je k dispozici také soubor s [kompletním exportem databáze](./eshop-db.sql))
2. stáhněte si složku **[eshop](./eshop)** se zdrojovým kódem projektu, nahrajte její obsah na server (a nezapomeňte na úpravu práv k adresářům *log* a *temp*)
3. v souboru **config/local.neon** přístupy k databázi, později také přístupy k FB loginu
4. upravte práva k adresáři *www/img/products* (nastavte práva 777)