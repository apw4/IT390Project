#!/usr/bin/php
<?php

ini_set("display_errors", 1);
ini_set("log_errors",1);
ini_set("error_log", "/var/logs/apache2/error.log");
error_reporting( E_ALL);

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('moviedata.php');

//$baseurl = "http://www.omdbapi.com/?apikey=788ab293"

function auth($user, $pass){ 
    ($db = mysqli_connect('%', 'hsagar111', '123456', 'auth'));
    if (mysqli_connect_errno()){
      echo "<br><br>Failed to connect to MYSQL<br><br> ". mysqli_connect_error();
      exit();
    }
    echo "Successfully connected to MySQL<br><br>";
    
    mysqli_select_db($db, 'auth' );
    $s = "select * from users where username = '$user' and password = '$pass'";
    ($t = mysqli_query ($db, $s)) or die(mysqli_error($db));
    $num = mysqli_num_rows($t);
    
    if ($num == 0)
    {
      return false;
    }
    else
    {
        $omdb = new OMDb();
        $omdb->setParams( ['tomatoes' => TRUE, 'plot' => 'full', 'apikey' => '788ab293'] );
      return true;
    }
}

function signup($user, $pass, $mail){
    ($db = mysqli_connect('%', 'hsagar111', '123456', 'auth'));
    if (mysqli_connect_errno()){
      echo "<br><br>Failed to connect to MYSQL<br><br> ". mysqli_connect_error();
      exit();
    }  
    echo "<br>Successfully connected to MySQL<br>";
    
    mysqli_select_db($db, 'auth');
    $s = "select * from users where email = '$mail' or username = '$user'"; 
    $t = mysqli_query($db, $s) or die(mysqli_error($db));
    $r = mysqli_fetch_array($t, MYSQLI_ASSOC);
    $u = $r["username"];
    $v = $r["email"];
    
    if ($user == $u){
        echo "<br><br>Error: That username is already in use.<br><br>";
        return false; 
    } else if ($mail == $v){
        echo "<br><br>Error: That email address is already in use.<br><br>";
        return false; 
    } else {
        mysqli_query($db, "insert into users (username, password, email) values ('$user', '$pass','$mail')");
        return true;
    }
}

function search($keyword){
    $results = $omdb->search($keyword);
    return $results;
}

function stats($id){
    $results = $omdb->get_by_id($id);
    return $results;
}
    
function requestProcessor($request){
  echo "received request".PHP_EOL;
  var_dump($request);
    
  if(!isset($request['type'])){
    return "Server rProcessor broke";
  }
    
  switch ($request['type']){
    case "login":
        return auth($request['user'], $request['pass']);
    case "signup":
        return signup($request['user'], $request['pass'], $request['mail']);
    case "search":
        return search($request['keyword']);
    case "stats":
        return stats($request['id']);
  }
    
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();

?>