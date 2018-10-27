<html lang="en">
<head>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
	<script>
		$(function(){
		 var keyStop = {
		   8: ":not(input:text, textarea, input:file, input:password, input:number)", // stop backspace = back
		   13: "input:text, input:password", // stop enter = submit 

		   end: null
		 };
		 $(document).bind("keydown", function(event){
		  var selector = keyStop[event.which];

		  if(selector !== undefined && $(event.target).is(selector)) {
			  event.preventDefault(); //stop event
		  }
		  return true;
		 });
		});
	</script>
	<style>
		fieldset {
			padding-top: 0.35em;
			padding-bottom: 0.625em;
			padding-left: 0.75em;
			padding-right: 0.75em;
			border: 2px groove (internal value); 
			width: 70%; 
			margin: auto; } 
		legend {
			font-size:160%; 
			margin-left: 2px;
			margin-right: 2px;
			padding-top: 0.35em;
			padding-bottom: 0.625em;
			padding-left: 0.75em;
			padding-right: 0.75em;}
	</style>
</head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<nav class="navbar navbar-default">
	<div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Oakville Online Banking</a>
    </div>	<ul class="nav navbar-nav navbar-right">
      <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li></ul>
  </div>
</nav>
	<fieldset id="fieldset">


<?php
include (  "account.php"     ) ;
function getdata ($name, &$result) {
global $db, $bad;
/* MAIN LOGIC IS LIKE THIS:
	$bad  = false ;
	getdata ( "a" , $a );
	getdata ( "b" , $b );
	if ( $bad ) { exit ("..."); 
*/
	if ( ! isset ($_GET[$name]) )
		{
			echo "<br> $name is undefined<br>";
			$bad = true;
			return;
		}
	
	if (empty($_GET[$name]))
		{
			echo "<br> $name == empty<br>";
			$bad = true;
			return;
		}
	
	$temp = $_GET[$name];
	
	$temp = mysqli_real_escape_string($db, $temp);
	
	$result = $temp;
	echo "<br>Result: $result";
	}

	
function auth ( $user , $pass, $db, &$t)  
	{    
		$s = " select * from A  where user = '$user' and pass= SHA1('$pass') " ;
		echo "<br>The SQL is: $s <br>";

		$t = mysqli_query ( $db , $s );

		if (mysqli_num_rows($t) > 0 ) 
			{ 
				return true  ;
			} 
		else 
			{   
				return false  ;
			}

	}
	
	
function deposit ( $user , $amount ) {
global $db;

    $s1 = "update A SET cur_balance = cur_balance + '$amount' where user = '$user'";
		echo "<br>Update SQL is: $s1";
		($t = mysqli_query($db, $s1)) or die(mysqli_error());
		$_SESSION ["current_balance"] = $_SESSION ["current_balance"] + $amount;

    $s2 = "insert into T value ( '$user' , 'D' , '$amount',  NOW() )";
		echo "<br>Update SQL is: $s2";
		($t = mysqli_query ($db, $s2)) or die(mysqli_error());

}


function withdraw ( $user , $amount ) {
//EXITS if there is an attempt to overdraw
global $db;
	if ($_SESSION ["current_balance"] < $amount)
		{
			$delay = $_SESSION["delay"];
			$message = "<br><div class='alert alert-danger'><strong>Error!</strong> '$amount' is greater than your current balance of " .$_SESSION ["current_balance"].". Please change your input.</div>";
			$target = "formpage.php";
			redirect ($message, $target, $delay);
		}
	
    $s1 = "update A SET cur_balance = cur_balance - '$amount' where user = '$user'";
		echo "<br>Update SQL is: $s1";
		($t = mysqli_query ($db, $s1)) or die(mysqli_error());
		$_SESSION ["current_balance"] = $_SESSION ["current_balance"] - $amount;

    $s2 = "insert into T value ( '$user' , 'W' , '$amount',  NOW() )";
		echo "<br>Update SQL is: $s2";
		($t = mysqli_query ($db, $s2)) or die(mysqli_error());
}

function show ( $user , &$out) {     
//gets A and T rows for user and stores output in result
global $db;

	$s1 = " select * from A  where user = '$user'" ;
	$t1 = mysqli_query($db, $s1);
		echo "<br>Number of rows retrieved is: " .mysqli_num_rows($t1);
		
	$s2 = " select * from T where user = '$user' order by date desc";
	$t2 = mysqli_query($db, $s2);
		echo "<br>Number of rows retrieved is: " .mysqli_num_rows($t2);
		echo "<br><br>";
		$out = "<div class='container-fluid'><div class='jumbotron'><h3>Transactions</h3>";
	while ($r = mysqli_fetch_array( $t1, MYSQLI_ASSOC)) {
		$out .= "<table class='table'><tbody>";
		$out .= "<tr><td>User is: '$user'</td></tr>";
		$out .= "<tr><td>Current balance is: " .$r['cur_balance'] ."</td></tr>";
		$out .= "</tbody></table><br><br><table class='table'><tbody>";
	}
	$out .= "<caption>Here are your previous transactions: </caption>";
	while ($r = mysqli_fetch_array( $t2, MYSQLI_ASSOC)) {
		$transtype = $r['type'];
		$out .= "<tr><td>On " .$r['date'];
		$out .= "</td><td>$" .$r['amount'];
		$out .= " was ";
		if ($transtype == 'D')
			{
				$out .= "<span style=\"color: green\">deposited</span> into the account.</td></tr>";
			}
		elseif ($transtype == 'W')
			{
				$out .= "<span style=\"color: red\">withdrawn</span> from the account.</td></tr>";
			}
	}
	$out .= "</tbody></table></div></div><br><br>";
}


function getamount ( $name , &$result ) {
global $db, $bad;

	if ( ! is_numeric ($_GET[$name]))
		{
			echo "<br> $name is not a number <br>";
			$bad = true;
			return;
		}
		
	if ( 0 > ($_GET[$name]))
		{
			echo "<br> $name is not greater than zero <br>";
			$bad = true;
			return;
		}	
		
	if ( ! isset ($_GET[$name]) )
		{
			echo "<br> $name is undefined<br>";
			$bad = true;
			return;
		}
	
	if (empty($_GET[$name]))
		{
			echo "<br> $name == empty<br>";
			$bad = true;
			return;
		}
	
	$temp = $_GET[$name];
	
	$temp = mysqli_real_escape_string($db, $temp);
	
	$result = $temp;
	echo "<br>Result: $result";
	
	//EXITS if not numeric or negative
	}
function mailer ($user, $out){
	global $db;
	date_default_timezone_set("America/New_York");
	$s = " select mail from A  where user = '$user'" ;
	$t = mysqli_query($db, $s);
	$r = mysqli_fetch_array( $t, MYSQLI_ASSOC);
	
	$to = $r['mail'];
	$message = $out;
	$subject = "Your Banking Reciept - " .date("l, Y-m-d h:i:sa (T)");
	
	mail ($to, $subject, $message);
}
function redirect ( $message, $target){
	
	header ("refresh: 3; url = $target");
	
	exit($message);
}
function gatekeeper() {
	if (!isset( $_SESSION ["logged"])) {
		$message = "<br>Log in information not found.<br>";
		$target = "login.html";
		redirect ($message, $target;
	}
}

?>
</fieldset>
</html>