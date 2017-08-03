<?php
include("header.php");
$con = connect();
$start_time = date('Y-m-d', strtotime('-30 days'));
$end_time = date('Y-m-d');
if($query = $con->prepare("SELECT b.email,(SUM(f.price)*0.1) as total,COUNT(f.flight_num) 
FROM Flight f JOIN ticket t JOIN purchases p JOIN booking_agent b 
WHERE t.flight_num = f.flight_num AND t.ticket_id = p.ticket_id AND b.booking_agent_id=p.booking_agent_id 
and p.purchase_date BETWEEN ? AND ?
GROUP BY b.email ORDER BY total DESC LIMIT 5"))
{
	$query->bind_param('ss',date_format(date_create($start_time),"Ymd"), date_format(date_create($end_time),"Ymd"));
	$query->execute();
	$results = $query->get_result();
	$commission_results = $results->fetch_all();
}
echo "<div id='commission'>Top Booking Agents</br>";
for ($var = 0;$var < count($commission_results);$var++)
{
	$commission = $commission_results[$var];
	$commission_total = $commission[1]*10;
	echo "$commission[0], $$commission_total / $$commission[1], $commission[2] tickets </br>";
}
echo "</div>";

if($query = $con->prepare("SELECT p.customer_email,COUNT(p.customer_email) as total 
FROM purchases p JOIN ticket t 
WHERE t.ticket_id = p.ticket_id
and p.purchase_date BETWEEN ? AND ?
GROUP BY p.customer_email ORDER BY total DESC LIMIT 5"))
{
	$query->bind_param('ss',date_format(date_create($start_time),"Ymd"), date_format(date_create($end_time),"Ymd"));
	$query->execute();
	$results = $query->get_result();
	$frequent_flyers = $results->fetch_all();
}
echo "<div id='frequent_flyers'>Frequent Flyers</br>";
for ($var = 0;$var < count($frequent_flyers);$var++)
{
	$flyers = $frequent_flyers[$var];
	echo "<a href='/frequent_flyer.php?user=$flyers[0]'>$flyers[0]</a>, $flyers[1] flights</br>";
}
echo "</div>";
$chart_data[] = array();
$chart_data[0] = ['Year','Ticket Sales'];
for ($var = 1;$var <= 12;$var++)
{
	$start_time = date('Y-m-d', mktime(0, 0, 0, $var, 1));
	$end_time = date('Y-m-d',mktime(0, 0, 0, $var+1, 1));
	if($query = $con->prepare("SELECT COUNT(ticket_id), ? FROM purchases WHERE purchase_date BETWEEN ? AND ? 
	GROUP by purchase_date"))
	{
		$query->bind_param('sss',date_format(date_create($start_time),"m/d/Y"), date_format(date_create($start_time),"Ymd"),date_format(date_create($end_time),"Ymd"));
		$query->execute();
		$results = $query->get_result();
		$ticket_sales = $results->fetch_all();
		if($ticket_sales != NULL)
		{
			$chart_data[$var] = array($ticket_sales[0][1], $ticket_sales[0][0]);	
		}
		else
		{
			$chart_data[$var] = array($start_time,0);	
		}
	}
}
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load("current", {packages:["imagebarchart"]});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {

	var data = google.visualization.arrayToDataTable(<?php echo json_encode($chart_data, JSON_NUMERIC_CHECK); ?>);

	var chart = new google.visualization.ImageBarChart(document.getElementById('chart_div'));

	chart.draw(data, {width: 400, height: 240, min: 0});
  }
</script>
<div id='chart'>
Monthly Breakdown of Year Ticket Sale
<div id="chart_div" style="width: 400px; height: 240px;"></div>
</div>
<?php
include("footer.php");
?>