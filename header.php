<?php include("database.php"); ?>
<html>
<link rel="stylesheet" href="styles.css" type="text/css">
<a href="main.php" class="header_links">Main Page</a>
<?php
function check_account_type()
{
	$con = connect();
	if(isset($_SESSION["logged_in"]) && $query = $con->prepare("SELECt account_type FROM Person WHERE username=?"))
	{
	$query->bind_param('s', mysqli_real_escape_string($con,$_SESSION["logged_in"]));
	$query->execute();
	$results = $query->get_result();
	return $results->fetch_all()[0][0];
	}
}
function check_airline()
{
	$con = connect();
	if(isset($_SESSION["logged_in"]) && $query = $con->prepare("SELECt airline_name FROM airline_staff WHERE username=?"))
	{
	$query->bind_param('s', mysqli_real_escape_string($con,$_SESSION["logged_in"]));
	$query->execute();
	$results = $query->get_result();
	return $results->fetch_all()[0][0];
	}
}
session_start();
if(!isset($_SESSION["logged_in"]))
{
	echo '<a href="register.php" class="header_links">Register</a>';
	echo '<form id="login_form" action="login.php" method="post">
	E-mail/username: <input type="text" name="login_name">
	Password: <input type="password" name="login_password">
	<input type="submit" name="login" value="Login"/>
	<?php //echo $error;?>
	</form>';
}
else
{
	echo '<a href="myflights.php" class="header_links">My Flights</a>';
	if(check_account_type() == "agent"){echo '<a href="commissions.php" class="header_links">Commissions</a>';}
	if(check_account_type() == "staff"){echo '<a href="control_panel.php" class="header_links">Control Panel</a>';
	echo '<a href="reports.php" class="header_links">Reports</a>';}
	echo '<a href="logout.php" class="header_links">Logout</a>';
}
?>
<form id="search_form" action="search.php" method="post">
	<input type="text" name="from" id="from" placeholder="From city/airport"/>
	<input type="date" name="from_time" id="from_time" value="<?php echo date('Y-m-d'); ?>"/>
	<input type="text" name="to" id="to" placeholder="To city/airport"/>
	<input type="date" name="to_time" id="to_time"/>
	<input type="submit" name="search" value="Search flights"/>
</form>