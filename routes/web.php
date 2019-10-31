<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->get('/emails/add',function() use ($router){
	return "				Trabalhando E-Mails!"; 
});

$router->post('/emails/add','EmailController@index');

$router->get('/', function () use ($router) {
    return "Home, nada para ver aqui";
});

$router->get('/emails/send',function() use ($router){
	return "				Preparando e efetuando envio de e-mails"; 
});
$router->post('/emails/send','EmailController@setup');