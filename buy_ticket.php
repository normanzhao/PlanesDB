<?php
include("header.php");
$con = connect();
if(!isset($_POST['buy']) && ($query = $con->prepare("SELECT flight_num, 
		airline_name, 
		DATE_FORMAT(departure_time, '%H:%i %m/%d/%Y'), 
		DATE_FORMAT(arrival_time, '%H:%i %m/%d/%Y'),
		departure_airport, 
		arrival_airport, 
		d.airport_city, 
		a.airport_city,
		price FROM Flight JOIN Airport d JOIN Airport a WHERE d.airport_name = departure_airport AND a.airport_name = arrival_airport
		AND airline_name=? AND flight_num=?")))
		{
			$query->bind_param('ss',$_GET['airline'],$_GET['flight_num'] );
			$query->execute();
			$results = $query->get_result();
			$flight = $results->fetch_all()[0];
			echo "<form id='buy_form' action='buy_ticket.php' method='post'></br></br></br>
			<div id='buy_info'>
				Airline: $flight[1] &nbsp Flight#: $flight[0]</br>
				Departs: $flight[4],$flight[6]&nbsp Arrives: $flight[5],$flight[7]</br>
				Departure Time: $flight[2]&nbsp Arriveal Time: $flight[3]</br>
				Price: \$$flight[8]</br>
				<input type='hidden' name='flight_num' value='".$_GET['flight_num']."'>
				<input type='hidden' name='airline' value='".$_GET['airline']."'>";
				if(check_account_type() == "agent")
				{
					echo "Customer email: <input type='text' name='customer_email'></br>";
				}
			echo "<input type='submit' name='buy' value='Buy'/>
			</div>
			</form>";
		}

else if(isset($_POST['buy']))
{
	$query = $con->query("SELECT MAX(ticket_id) FROM ticket;");
	$ticket_id = (int)$query->fetch_all()[0][0] + 1;
	if($query = $con->prepare("INSERT INTO ticket(ticket_id,airline_name,flight_num) VALUES(?,?,?)"))
	{
		$query->bind_param('isi',$ticket_id,$_POST['airline'],$_POST['flight_num']);
		$query->execute();
	}
	if((check_account_type() == "customer") && $query = $con->prepare("INSERT INTO purchases(ticket_id,customer_email,purchase_date) VALUES(?,?,?)"))
	{
		$query->bind_param('iss',$ticket_id,$_SESSION["logged_in"],date("Y-m-d"));
		$query->execute();
	}
	else if((check_account_type() == "agent") && $insert = $con->prepare("INSERT INTO purchases(ticket_id,customer_email,booking_agent_id,purchase_date) VALUES(?,?,?,?)"))
	{
		if($query = $con->prepare("SELECT booking_agent_id FROM booking_agent WHERE email=?;"))
		{
			$query->bind_param('s', $_SESSION["logged_in"]);
			$query->execute();
			$results = $query->get_result();
			$agent_id = (int)$results->fetch_all()[0][0];
		}
		$insert->bind_param('isis',$ticket_id,$_POST['customer_email'],$agent_id,date("Y-m-d"));
		$insert->execute();
	}
	header( 'Location: myflights.php' );
}
?>
