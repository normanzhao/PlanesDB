<?php
function connect()
{
	$con = mysqli_connect("127.0.0.1", "root", "", "dbproject") or die("Connection to server died from error" . mysqli_error());
	return $con;
}

function disconnect($con)
{
	mysqli_close($con);
}
?>