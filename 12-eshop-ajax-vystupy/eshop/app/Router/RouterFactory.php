<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory{
	use Nette\StaticClass;

	public static function createRouter():RouteList {
    $adminRouter = new RouteList('Admin');
    $adminRouter->addRoute('admin/<presenter=Dashboard>/<action=default>[/<id>]');

	  $frontRouter = new RouteList('Front');
    $frontRouter->addRoute('produkty[/kategorie-<category>]', 'Product:list');  //pokud je do adresy zakomponována také proměnná category, je doplněna do adresy
    $frontRouter->addRoute('produkty[/kategorie-<category>]/<url>', 'Product:show');  //pokud je do adresy zakomponována také proměnná category, je doplněna prostřední část adresy
		$frontRouter->addRoute('<presenter=Homepage>/<action=default>[/<id>]');

	  $router = new RouteList();
		$router->add($adminRouter);
		$router->add($frontRouter);
		return $router;
	}
}
