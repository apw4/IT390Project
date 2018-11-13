<?php
include('realRMQlient.php');
$user = $_POST['user'];
$pass = $_POST['pass'];

if(!auth($user, $pass)){
	echo "\n Error: Username and Password combination not found";
	header("refresh:3; url=login.html");  
}else{
	echo "\n Welcome, '$user'<br>"; 
	header("refresh:3; url=frontpage.html"); 
} 
?>