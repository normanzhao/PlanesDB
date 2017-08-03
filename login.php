<?php
include("header.php");
$con = connect();
if($query = $con->prepare("SELECt password FROM Person WHERE username=?"))
{
$query->bind_param('s', mysqli_real_escape_string($con,$_POST["login_name"]));
$query->execute();
$results = $query->get_result();
$password = $results->fetch_all();
if(count($password) != 0)
{
	if(md5($_POST["login_password"]) == $password[0][0])
	{
		$_SESSION["logged_in"] = $_POST["login_name"];
		if((check_account_type() == "customer") || (check_account_type() == "agent"))
		{
			header( 'Location: myflights.php' );
		}
		else
		{
			header( 'Location: main.php' );
		}
	}
}
echo "<div id='logout_text'>Invalid credentials...redirecting to main page</div>";
echo '<meta http-equiv="refresh" content="1;url=main.php">';
}
?>
