<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();

$routes->add(
	'apiV1.example',
	new Route('/api/v1/example/',
		[
			'_controller' => Api\Controllers\Example::class,
			'_method' => 'exampleApi',
			#'_authorization' => true
		],
		[], [], '', [], 'GET'
	)
);

return $routes;
