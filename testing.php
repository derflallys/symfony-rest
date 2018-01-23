<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/01/18
 * Time: 03:40
 */
require __DIR__.'/vendor/autoload.php';

$client = new \GuzzleHttp\Client([
   'base_url' => 'http://localhost:8000',
    'defaults' => [
        'exceptions' =>false,
    ]
]);

$nickname = 'ObjectOrienter'.rand(0,999);
$data = array(
    'nickname'  => $nickname,
    'avatarNumber' => 5,
    'tagLine' => 'a test dev!'
);
//1 - Create a programmer resource
$response = $client->post('/api/programmers',[
    'body'=>json_encode($data)
]);

$programmerUrl = $response->getHeader('Location');
//2 GET a programmer resource
$response = $client->get($programmerUrl);

echo $response;
echo "\n\n";

//3 GET all programmer resource
$response = $client->get('/api/programmers');

echo $response;
echo "\n\n";