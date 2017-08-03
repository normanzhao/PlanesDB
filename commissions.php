<?php
include("header.php");
$con = connect();
?>
<form id="time_form" action="commissions.php" method="post">
	<input type="date" name="start_time" id="start_time" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>"/>
	<input type="date" name="end_time" id="end_time" value="<?php echo date('Y-m-d'); ?>"/>
	<input type="submit" name="lookup" value="Search"/>
</form>
<?php
$start_time = date('Y-m-d', strtotime('-30 days'));
$end_time = date('Y-m-d');
if(isset($_POST["lookup"]))
{
	$start_time = $_POST["start_time"];
	$end_time = $_POST["end_time"];
}
if($query = $con->prepare("SELECT (SUM(f.price)*0.1),COUNT(f.flight_num) FROM Flight f JOIN ticket t JOIN purchases p JOIN booking_agent b WHERE t.flight_num = f.flight_num
	AND t.ticket_id = p.ticket_id AND b.booking_agent_id=p.booking_agent_id AND b.email = ? AND p.purchase_date BETWEEN ? AND ?;"))
	{
		$query->bind_param('sss',$_SESSION["logged_in"],date_format(date_create($start_time),"Ymd"), date_format(date_create($end_time),"Ymd"));
		$query->execute();
		$results = $query->get_result();
		$commission_results = $results->fetch_all()[0];
	}
$commission_total = $commission_results[0]*10;
echo "<div id='commission'>
		Total Sales: $$commission_total</br>
		Total Commission: $$commission_results[0]</br>
		Total Tickets Sold: $commission_results[1]</br>
	</div>";
include("footer.php");
?>