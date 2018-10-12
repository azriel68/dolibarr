<?php

use Slim\Http\Request;
use Slim\Http\Response;
// Routes

$app->get('/test/{name}', function ($request, $response, $args) {
	global $user;
	$session = $request->getAttribute('session'); //get the session from the request

	//return $response->write('Yay, ' . $args['name']. ' '. $user->getNomUrl(1));
	return $this->renderer->render($response, 'index.phtml',  array_merge($args, ['url'=>$user->getNomUrl(1) ] ));
});

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

