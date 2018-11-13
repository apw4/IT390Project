#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


function auth ($user, $pass){
    $client = new rabbitMQClient("testRabbitMQ.ini","testServer");
    if (isset($argv[1]))
    {
      $msg = $argv[1];
    }
    else
    {
      $msg = "Client auth broke";
    }
    
    $request = array();
    $request['type'] = "login";
    $request['user'] = $user;
    $request['pass'] = $pass;
    $request['message'] = $msg;
    $response = $client->send_request($request);
    return $response; 
    echo $argv[0]." END".PHP_EOL;
} 

function signup ($user, $pass, $mail){
    $client = new rabbitMQClient("testRabbitMQ.ini","testServer");
    if (isset($argv[1]))
    {
      $msg = $argv[1];
    }
    else
    {
      $msg = "Client signup broke";
    }
    
    $request = array();
    $request['type'] = "signup";
    $request['user'] = $user;
    $request['pass'] = $pass;
    $request['mail'] = $mail;
    $request['message'] = $msg;
    $response = $client->send_request($request);
    return $response; 
    echo $argv[0]." END".PHP_EOL;
} 
?>