<!DOCTYPE html>

<html lang="en">
<body>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);  
ini_set('display_errors' , 1);

include (  "functions.php" ) ;

$db = mysqli_connect($hostname, $username, $password , $project);

if (mysqli_connect_errno())
  {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	  exit();
  }
print "<br>Successfully connected to MySQL.<br>";

mysqli_select_db( $db, $project ); 


echo "If this statement is reached then retrieval service is provided. <br>";



$bad = false;
getdata ("user", $user);
getdata ("pass", $pass);
$t;

if(! auth ($user, $pass, $db, $t)) 
	{	$message = "<br><div class='alert alert-danger'><strong>Error!</strong> That password and username combination is incorrect.</div>";
		$target = "login.html";
		redirect ($message, $target);
	}
else 
	{  //session_set_cookie_params ( 0, "/~apw4/", "web.njit.edu");   //WEEK10
		session_start();
		$sidvalue = session_id();
	
		$_SESSION ["logged"] = true;
		$_SESSION ["user"] = $user;
		$r = mysqli_fetch_array($t, MYSQLI_ASSOC);
		/*$_SESSION ["current_balance"] = $r['cur_balance'];
		$_SESSION ["fullname"] = $r['fullname'];*/
		$_SESSION ['mail'] = $r['mail']; 
		
		$message = "<br><div class='alert alert-success'><strong>Success!</strong> Log in successful.</div>";
		/*if(isset($_COOKIE['lastvisited'])) {
			$visit = $_COOKIE['lastvisited'];
			$message .= "<div class='alert alert-info'>Your Session ID is: " .$sidvalue ."<br>Your last visit was on " . $visit ."</div><br>";
		}
		else {*/
			$message .= "<div class='alert alert-info'>Your Session ID is: " .$sidvalue ."</div><br>";
		//}
		$target = "formpage.php";
		redirect ($message, $target);
	}


?>
</body>
</html>