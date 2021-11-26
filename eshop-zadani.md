# Požadavky na semestrální práci - eshop

Jak už víte, vaším úkolem je vytvořit jednoduchý e-shop napsaný v Nette. Toto zadání je ale příliš obecné. Pojďme je trochu konkretizovat.

## Co budeme prodávat?
:point_right:
- prvním úkolem vašeho týmu bude domluvit se ve dvojici na tom, co konkrétně budete prodávat (na co bude e-shop specializovat)
- na typu zboží samozřejmě bude záležet to, kolik vlastností bude zboží mít, jestli podle nich půjde filtrovat atp.

## Funkce vyvíjeného e-shopu
### Konkrétní povinné funkcionality
:point_right:
- **administrační rozhraní**
  - správa uživatelů
  - správa zboží - u zboží bude kromě textových informací také alespoň 1 fotka
  - správa kategorií
  - správa objednávek (zobrazení, změna stavu)
  
- **uživatelské rozhraní**
  - zobrazení zboží
  - přidání zboží do košíku
  - odeslání objednávky
  
### Povinné zesložitění - vyberte si jednu z položek
:point_right:
- zboží bude ve více kategoriích, či bude použit ještě jiný způsob filtrování (např. podle barvy, materiálu, velikosti atp.)
- bude kontrolován počet kusů skladem - nepůjde objednat zboží, které skladem není
- ke zboží půjde přidávat komentáře
- u zboží bude větší množství fotek

## Uživatelské rozhraní
:point_right:
- pokud znáte nějaký CSS framework pro jednodušší tvorbu UI, doporučuji ji využít (např. Bootstrap) 
- uživatelská část by měla být pokud možno použitelná z počítače i z mobilu
- administrace bude používána jen z počítače

---

## Termíny pro realizaci
:point_right:
- **5.12.2021** - odevzdání první části administračního rozhraní
- na cvičení **10.12.2021** či **17.12.2021** odprezentovat první část e-shopu a cílový stav (doporučuji 17.12., délka prezentace max. 5 minut)
- **termín ve zkouškovém** - odevzdání aplikace formou krátké obhajoby

## Co si ukážeme na cvičeních?
:point_right:
Některé funkce nemusíte vymýšlet sami - ukážeme si jejich tvorbu na cvičení či v rámci nápovědné prezentace. Určitě můžete počítat s:
  - návrhem datového modelu a struktury aplikace (na cvičení 26.11.2021)
  - editací položky v administraci - uložení fotky produktu
  - přidávání položek do košíku, odeslání objednávky
  - nastavení vlastní struktury URL adres (routing)

:point_right:
Kromě toho si společně ukážeme:
  - jak do aplikace zakomponovat AJAX
  - jak upravit CSS knihovnu pomocí SCSS
