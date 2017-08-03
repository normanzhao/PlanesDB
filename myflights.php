<?php
include("search.php");
$con = connect();
if(check_account_type() == "customer")
{
	search_flights("myflights","","","","",$_SESSION["logged_in"]);
}
else if(check_account_type() == "agent")
{
	search_flights("agents_myflights","","","","",$_SESSION["logged_in"]);
}
else if(check_account_type() == "staff")
{
	search_flights("staff_myflights","","","","",$_SESSION["logged_in"]);
}
?>
