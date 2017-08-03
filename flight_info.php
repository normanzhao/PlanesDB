<?php
include("header.php");
$con = connect();
echo "<table>";
	echo "<tr>
		<th>Customer Email</th>
		<th>Used Booking Agent?</th></tr>";
if($query = $con->prepare("SELECT p.customer_email, CASE WHEN p.booking_agent_id IS NULL THEN 'No' ELSE 'Yes' END 
FROM purchases p JOIN ticket t WHERE t.ticket_id = p.ticket_id AND t.airline_name = ? and t.flight_num=?"))
{
	$query->bind_param('si', mysqli_real_escape_string($con,$_GET['airline']), mysqli_real_escape_string($con,$_GET['flight_num']));
	$query->execute();
	$results = $query->get_result();
	$flights = $results->fetch_all();
}
else
{
	die("</table>");
}
for ($var = 0;$var < count($flights);$var++)
{
	$current_row = $flights[$var];
	echo "<tr>";
	echo "<td>$current_row[0]</td><td>$current_row[1]</td>";
	echo "</tr>";
}
echo "</table>";

include("footer.php");
?>