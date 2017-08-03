<?php
include("header.php");
function search_flights($usage,$from="",$from_time="",$to="",$to_time="",$user="",$airline="")
{
	echo "<table>";
	echo "<tr>
		<th>Flight #</th>
		<th>Departs</th>
		<th>Arrives</th>
		<th>Status</th>";
	if($usage == "myflights")
	{
		echo "<th>Amount</th>";
	}
	if($usage == "agents_myflights")
	{
		echo "<th>For</th>";
		echo "<th>Amount</th>";
	}
	echo "</tr>";
	$con = connect();
	if($usage=="main")
	{
		$query = $con->query("SELECT flight_num, 
		airline_name, 
		DATE_FORMAT(departure_time, '%H:%i %m/%d/%Y'), 
		DATE_FORMAT(arrival_time, '%H:%i %m/%d/%Y'),
		departure_airport, 
		arrival_airport, 
		d.airport_city, 
		a.airport_city,
		status FROM Flight JOIN Airport d JOIN Airport a WHERE d.airport_name = departure_airport AND a.airport_name = arrival_airport ORDER BY flight_num asc");
		$flights = $query->fetch_all();
	}
	else if($usage=="myflights")
	{
		if($query = $con->prepare("SELECT f.flight_num, 
		f.airline_name, 
		DATE_FORMAT(f.departure_time, '%H:%i %m/%d/%Y'), 
		DATE_FORMAT(f.arrival_time, '%H:%i %m/%d/%Y'),
		f.departure_airport, 
		f.arrival_airport, 
		d.airport_city, 
		a.airport_city,
		status, 
		count(f.flight_num) FROM Flight f JOIN Airport d JOIN Airport a JOIN ticket t JOIN purchases p WHERE d.airport_name = departure_airport AND a.airport_name = arrival_airport AND t.flight_num = f.flight_num
		AND t.ticket_id = p.ticket_id AND p.customer_email = ?
		GROUP BY flight_num asc"))
		{
			$query->bind_param('s', mysqli_real_escape_string($con,$user));
			$query->execute();
			$results = $query->get_result();
			$flights = $results->fetch_all();
		}
	}
	else if($usage=="agents_myflights")
	{
		if($query = $con->prepare("SELECT f.flight_num, 
		f.airline_name, 
		DATE_FORMAT(f.departure_time, '%H:%i %m/%d/%Y'), 
		DATE_FORMAT(f.arrival_time, '%H:%i %m/%d/%Y'),
		f.departure_airport, 
		f.arrival_airport, 
		d.airport_city, 
		a.airport_city,
		status, 
		p.customer_email,
		count(f.flight_num) FROM Flight f JOIN Airport d JOIN Airport a JOIN ticket t JOIN purchases p JOIN booking_agent b WHERE d.airport_name = departure_airport AND a.airport_name = arrival_airport AND t.flight_num = f.flight_num
		AND t.ticket_id = p.ticket_id AND b.booking_agent_id=p.booking_agent_id AND b.email = ?
		GROUP BY p.customer_email asc"))
		{
			$query->bind_param('s', mysqli_real_escape_string($con,$user));
			$query->execute();
			$results = $query->get_result();
			$flights = $results->fetch_all();
		}
	}
	else if($usage=="staff_myflights")
	{
		if($query = $con->prepare("SELECT f.flight_num, 
		f.airline_name, 
		DATE_FORMAT(f.departure_time, '%H:%i %m/%d/%Y'), 
		DATE_FORMAT(f.arrival_time, '%H:%i %m/%d/%Y'),
		f.departure_airport, 
		f.arrival_airport, 
		d.airport_city, 
		a.airport_city,
		status FROM Flight f JOIN Airline_Staff s JOIN Airport d JOIN Airport a JOIN ticket t JOIN purchases p WHERE d.airport_name = departure_airport AND a.airport_name = arrival_airport
		AND f.airline_name=s.airline_name AND  s.username= ?
		GROUP BY flight_num order by Status desc"))
		{
			$query->bind_param('s', mysqli_real_escape_string($con,$user));
			$query->execute();
			$results = $query->get_result();
			$flights = $results->fetch_all();
		}
	}
	else if($usage=="flyer")
	{
		if($query = $con->prepare("SELECT f.flight_num, 
		f.airline_name, 
		DATE_FORMAT(f.departure_time, '%H:%i %m/%d/%Y'), 
		DATE_FORMAT(f.arrival_time, '%H:%i %m/%d/%Y'),
		f.departure_airport, 
		f.arrival_airport, 
		d.airport_city, 
		a.airport_city,
		status FROM Flight f JOIN Airport d JOIN Airport a JOIN purchases p JOIN ticket t WHERE d.airport_name = departure_airport AND a.airport_name = arrival_airport and t.ticket_id=p.ticket_id AND 
		p.customer_email = ? AND f.airline_name=?
		 GROUP BY flight_num asc"))
		{
			$query->bind_param('ss', $user,$airline);
			$query->execute();
			$results = $query->get_result();
			$flights = $results->fetch_all();
		}
		else
		{
			die("</table>");
		}
		
	} 
	else if($usage=="search")
	{
		if($query = $con->prepare("SELECT flight_num, 
		airline_name, 
		DATE_FORMAT(departure_time, '%H:%i %m/%d/%Y'), 
		DATE_FORMAT(arrival_time, '%H:%i %m/%d/%Y'),
		departure_airport, 
		arrival_airport, 
		d.airport_city, 
		a.airport_city,
		status FROM Flight JOIN Airport d JOIN Airport a WHERE d.airport_name = departure_airport AND a.airport_name = arrival_airport
		AND (departure_airport=? OR d.airport_city=?) AND (arrival_airport=? OR a.airport_city=?) AND departure_time>=? AND arrival_time>=? ORDER BY flight_num asc"))
		{
			$query->bind_param('ssssss', mysqli_real_escape_string($con,$from),mysqli_real_escape_string($con,$from), mysqli_real_escape_string($con,$to),mysqli_real_escape_string($con,$to), date_format(date_create($from_time),"Ymd"), date_format(date_create($to_time),"Ymd"));
			$query->execute();
			$results = $query->get_result();
			$flights = $results->fetch_all();
		}
		else
		{
			die("</table>");
		}
		
	} 

	for ($var = 0;$var < count($flights);$var++)
	{
		$current_row = $flights[$var];
		echo "<tr>";
		if(isset($_SESSION["logged_in"]) && (check_account_type() != "staff") && ($current_row[8] == "Upcoming") && $usage != "myflights")
		{
			echo "<td><a href='/buy_ticket.php?flight_num=$current_row[0]&airline=$current_row[1]'>$current_row[1]#$current_row[0]</a></td>";
		}
		else if($usage == "staff_myflights")
		{
			echo "<td><a href='/flight_info.php?flight_num=$current_row[0]&airline=$current_row[1]'>$current_row[1]#$current_row[0]</a></td>";
		}
		else{echo "<td>$current_row[1]#$current_row[0]</td>";}
		echo "<td>$current_row[2] from $current_row[4],$current_row[6]</td>
		<td>$current_row[3] at $current_row[5],$current_row[7]</td>
		<td>$current_row[8]</td>";	
		if($usage == "myflights")
		{
			echo "<td>$current_row[9] ticket(s)</td>";
		}
		if($usage == "agents_myflights")
		{
			echo "<td>$current_row[9]</td>";
			echo "<td>$current_row[10] ticket(s)</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
	disconnect($con);		
}
if(isset($_POST["from"]) and isset($_POST["from_time"]) and isset($_POST["to"]) and isset($_POST["to_time"]))
{	
	search_flights("search",$_POST["from"],$_POST["from_time"],$_POST["to"],$_POST["to_time"]);
}
include("footer.php");
?>
