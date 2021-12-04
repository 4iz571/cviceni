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

