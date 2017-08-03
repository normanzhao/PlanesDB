<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.getElementById("status_form").style.display = "none";  
		document.getElementById("new_airplane_form").style.display = "none";  
		document.getElementById("new_airport_form").style.display = "none";  
	}, false);
  function setFlight(){
    var checked = document.getElementById("new_flight");
    if(checked.checked)
      document.getElementById("new_flight_form").style.display = "block";  
	  document.getElementById("status_form").style.display = "none";  
	  document.getElementById("new_airplane_form").style.display = "none";   
	  document.getElementById("new_airport_form").style.display = "none";   
	  document.getElementById("flight_status").checked = false;
	  document.getElementById("new_airplane").checked = false;
	  document.getElementById("new_airport").checked = false;
  } 
  function changeFlight(){
    var checked = document.getElementById("flight_status");
    if(checked.checked)
      document.getElementById("new_flight_form").style.display = "none";  
	  document.getElementById("status_form").style.display = "block";   
	  document.getElementById("new_airplane_form").style.display = "none";
	  document.getElementById("new_airport_form").style.display = "none";   
	  document.getElementById("new_flight").checked = false;
	  document.getElementById("new_airplane").checked = false;
	  document.getElementById("new_airport").checked = false;
  } 
  function setAirplane(){
    var checked = document.getElementById("new_airplane");
    if(checked.checked)
      document.getElementById("new_flight_form").style.display = "none";  
	  document.getElementById("status_form").style.display = "none";  
	  document.getElementById("new_airplane_form").style.display = "block"; 
	  document.getElementById("new_airport_form").style.display = "none";   
	  document.getElementById("new_flight").checked = false;
	  document.getElementById("flight_status").checked = false;	  
	  document.getElementById("new_airport").checked = false;
  } 
  function setAirport(){
    var checked = document.getElementById("new_airport");
    if(checked.checked)
      document.getElementById("new_flight_form").style.display = "none";  
	  document.getElementById("status_form").style.display = "none";  
	  document.getElementById("new_airplane_form").style.display = "none"; 
	  document.getElementById("new_airport_form").style.display = "block";   
	  document.getElementById("new_flight").checked = false;
	  document.getElementById("flight_status").checked = false;	  
	  document.getElementById("new_airplane").checked = false;
  } 
</script>
<?php
include("header.php");
$con = connect();
$error = "";
function airplane_selector()
{
	$con = connect();
	if($query = $con->prepare("SELECT airplane_id FROM Airplane WHERE airline_name=?"))
	{
	$query->bind_param('s', mysqli_real_escape_string($con,check_airline()));
	$query->execute();
	$results = $query->get_result();
	$airplanes = $results->fetch_all();
	echo '<select name="airplane" size="1">';
	for ($var = 0;$var < count($airplanes);$var++)
	{
		$current_row = $airplanes[$var];
		echo "<option value='$current_row[0]'>$current_row[0]</option>";
	}
	echo '</select>';
	disconnect($con);
	}
}
function flight_selector()
{
	$con = connect();
	if($query = $con->prepare("SELECT flight_num FROM Flight WHERE airline_name=?"))
	{
	$query->bind_param('s', mysqli_real_escape_string($con,check_airline()));
	$query->execute();
	$results = $query->get_result();
	$flights = $results->fetch_all();
	echo '<select name="flights" size="1">';
	for ($var = 0;$var < count($flights);$var++)
	{
		$current_row = $flights[$var];
		echo "<option value='$current_row[0]'>$current_row[0]</option>";
	}
	echo '</select>';
	disconnect($con);
	}
}
function airport_selector($elementname)
{
	$con = connect();
	if($query = $con->query("SELECT airport_name FROM Airport"))
	{
	$airports = $query->fetch_all();
	echo "<select name=$elementname size='1'>";
	for ($var = 0;$var < count($airports);$var++)
	{
		$current_row = $airports[$var];
		echo "<option value='$current_row[0]'>$current_row[0]</option>";
	}
	echo '</select>';
	disconnect($con);
	}
}


if(isset($_POST["edit_changes"]))
{
	if($_POST["edit_type"] == "new_flight")
	{
		$query = $con->query("SELECT MAX(flight_num) FROM Flight");
		$flight_num = (int)($query->fetch_all()[0][0])+1;
		$query = $con->prepare("INSERT INTO flight (airline_name,flight_num,departure_airport,departure_time,arrival_airport,arrival_time,
		price,status,airplane_id)
		VALUES (?,?,?,?,?,?,?,?,?);");
		$query->bind_param('sissssisi', check_airline(),
		$flight_num,
		mysqli_real_escape_string($con,$_POST["flight_from"]),
		mysqli_real_escape_string($con,$_POST["flight_from_time"]),
		mysqli_real_escape_string($con,$_POST["flight_to"]),
		mysqli_real_escape_string($con,$_POST["flight_to_time"]),
		mysqli_real_escape_string($con,$_POST["flight_price"]),
		$defaultval = "Upcoming",
		mysqli_real_escape_string($con,$_POST["airplane"]));
		$query->execute();
	}
	else if($_POST["edit_type"] == "flight_status")
	{
		$query = $con->prepare("UPDATE Flight SET status=? WHERE flight_num=?");
		$query->bind_param('si', mysqli_real_escape_string($con,$_POST["new_status"]),
		mysqli_real_escape_string($con,$_POST["flights"]));
		$query->execute();
	}
	else if($_POST["edit_type"] == "new_airplane")
	{	
		$query = $con->query("SELECT MAX(airplane_id) FROM Airplane");
		$airplane_id = (int)($query->fetch_all()[0][0])+1;
		$query = $con->prepare("INSERT INTO Airplane (airline_name,airplane_id,seats)
		VALUES (?,?,?);");
		$query->bind_param('sii', check_airline(),
		$airplane_id,
		mysqli_real_escape_string($con,$_POST["airplane_seats"]));
		$query->execute();
	}
	else if($_POST["edit_type"] == "new_airport")
	{	
		$query = $con->prepare("INSERT INTO Airport (airport_name,airport_city)
		VALUES (?,?);");
		$query->bind_param('ss', mysqli_real_escape_string($con,$_POST["airport_name"]),
		mysqli_real_escape_string($con,$_POST["airport_city"]));
		$query->execute();
	}
	header( 'Location: main.php' );
}
disconnect($con);
?>
<form id="panel_form" action="control_panel.php" method="post">
	</br></br></br>
	<div id="edit_type">
		<input type="radio" id="new_flight" name="edit_type" value="new_flight" onchange="setFlight();" checked="checked"> New Flight
		<input type="radio" id="flight_status" name="edit_type" value="flight_status" onchange="changeFlight();"> Change Flight Status
		<input type="radio" id="new_airplane" name="edit_type" value="new_airplane" onchange="setAirplane();"> New Airplane
		<input type="radio" id="new_airport" name="edit_type" value="new_airport" onchange="setAirport();"> New Airport
	</div>
	</br>
	<div id="control_panel">
		<div id="new_flight_form">
			From Airport: <?php airport_selector("flight_from"); ?>
			From Time: <input type="date" name="flight_from_time" id="flight_from_time"/></br>
			To Airport: <?php airport_selector("flight_to"); ?>
			To Time: <input type="date" name="flight_to_time" id="flight_to_time"/></br>
			Flight Price: <input type="text" name="flight_price" id="flight_price"/>
			Flight plane#: <?php airplane_selector(); ?></br>
		</div>
		<div id="status_form">
			Flight#: <?php flight_selector();?></br>
			Status: <select name="new_status">
					  <option value="Upcoming">Upcoming</option>
					  <option value="Delayed">Delayed</option>
					  <option value="In-progress">In-progress</option>
					</select>
		</div>
		<div id="new_airplane_form">
			Seats: <input type="text" name="airplane_seats" id="airplane_seats"/>
		</div>
		<div id="new_airport_form">
			Airport Name: <input type="text" name="airport_name">
			Airport City: <input type="text" name="airport_city"></br>
		</div>
	</br>
	<input type="submit" name="edit_changes" value="Submit"/>
	<?php echo $error;?>
</form>
<?php
include("footer.php");
?>