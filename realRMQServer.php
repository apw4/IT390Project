#!/usr/bin/php
<?php

ini_set("display_errors", 1);
ini_set("log_errors",1);
ini_set("error_log", "/var/logs/apache2/error.log");
error_reporting( E_ALL);

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('omdb.class.php');

//$baseurl = "http://www.omdbapi.com/?apikey=788ab293"

global $omdb;
$omdb = new OMDb();
$omdb->setParams( ['tomatoes' => TRUE, 'plot' => 'full', 'apikey' => '788ab293'] );

function getDb() {
  ($db = mysqli_connect('localhost', 'IT490', '$It4902018', 'films'));
  if (mysqli_connect_errno()){
    echo "<br><br>Failed to connect to MYSQL<br><br> ". mysqli_connect_error();
    exit();
  }  
  echo "<br>Successfully connected to MySQL<br>";
  
  mysqli_select_db($db, 'films');
  return $db;
}    

function auth($user, $pass){ 
    $db = getDb();
    $s = "select * from users where username = '$user' and password = '$pass'";
    ($t = mysqli_query($db, $s)) or die(mysqli_error($db));
    $num = mysqli_num_rows($t);
    if ($num == 0)
    {
      return false;
    }
    else
    {
      return true;
    }
}

function getUserInfo($user){
    $db = getDb();
    $s = "select * from users where username = '$user'";
    ($t = mysqli_query($db, $s)) or die(mysqli_error($db));    
    $user = mysqli_fetch_array($t, MYSQLI_ASSOC);
    $num = mysqli_num_rows($t);
    if ($num == 0)
    {
      return $user;
    }
    else
    {
      return "User not founds";
    }
}


function favoriteMovie($user_id, $movie_id){
    $db = getDb();
    $s = "select * from favorite_movies where movie_id = '$movie_id' and user_id = '$user_id'";
    ($t = mysqli_query($db, $s)) or die(mysqli_error($db));    
    $num = mysqli_num_rows($t);
    if ($num == 0)
    {
      $s = "insert into favorite_movies (user_id, movie_id) values ('$user_id', '$movie_id')";
      ($t = mysqli_query($db, $s)) or die(mysqli_error($db));
      return "Movie successfully added to your favorites list";
    }
    else
    {
      return "Movie is already in your favorites";
    }
}

function unFavoriteMovie($user_id, $movie_id){
    $db = getDb();
    $s = "delete from favorite_movies  where movie_id = '$movie_id' and user_id = '$user_id'";
    ($t = mysqli_query($db, $s)) or die(mysqli_error($db));
    return "Movie successfully removed from your favorites list";
}

function signup($user, $pass, $mail){
    $db = getDb();
    $s = "select * from users where email = '$mail' or username = '$user'"; 
    $t = mysqli_query($db, $s) or die(mysqli_error($db));
    $r = mysqli_fetch_array($t, MYSQLI_ASSOC);
    $u = $r["username"];
    $v = $r["email"];
    printf($r);
    	echo "We should have the stuff by now, do we?";
	echo $u;
	echo $v;


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
  $omdb = new OMDb();
  $omdb->setParams( ['tomatoes' => TRUE, 'plot' => 'full', 'apikey' => '788ab293'] );
  $results = $omdb->search($keyword);
  return $results;
}

function stats($id){
    // $results = $omdb->get_by_id($id);
    // return $results;
}

function requestProcessor($request){
  echo "received request".PHP_EOL;
  var_dump($request);
    
  if(!isset($request['type'])){
    return "Server rProcessor broke";
  }
    
  switch ($request['type']){
    case "login":
    	$x = auth($request['user'], $request['pass']);
    	if ($x){
    		$y = "true.";
    	} else if (!$x) {
    		$y = "false.";
      }
    	echo "Server is sending the shit. It's ". $y;
    	return $x;
    case "getUserInfo":
      return getUserInfo($request['user_id']);
    case "favoriteMovie":
      return favoriteMovie($request['user_id'], $request['movie_id']);
    case "unFavoriteMovie":
      return unFavoriteMovie($request['user_id'], $request['movie_id']);
    case "signup":
      return signup($request['user'], $request['pass'], $request['mail']);
    case "search":
      $x = search($request['keyword']);
    	foreach ($x['Search'] as $result) {
     	echo implode(" ", $result);}
    	return $x;
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
