<?php
include('realRMQClient.php');

$page = $_GET['page'];
$user = $_GET['user'];
$pass = $_GET['pass'];

switch ($page){
    case "signup":
        $mail = $_GET['email'];
        $response = signup($user, $pass, $mail);
        if(!$response){
            echo "\n Error: Registration failed";
            header("refnresh:3; url=signup.html");  
        }else{
            echo "\n Signup Succesful"; 
            header("refresh:3; url=login.html"); 
        }
    case "login":
        $response = auth($user, $pass);
        if(!$response){
            echo "\n Error: Username and Password combination not found";
            header("refnresh:3; url=login.html");  
        }else{
            echo "\n Welcome, '$user'<br>"; 
            header("refresh:3; url=frontpage.html"); 
        }
}

?>