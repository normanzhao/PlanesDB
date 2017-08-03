<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.getElementById("agent_form").style.display = "none";  
		document.getElementById("staff_form").style.display = "none";  
	}, false);
  function setCustomer(){
    var checked = document.getElementById("customer");
    if(checked.checked)
      document.getElementById("customer_form").style.display = "block";  
	  document.getElementById("agent_form").style.display = "none";  
	  document.getElementById("staff_form").style.display = "none";   
	  document.getElementById("agent").checked = false;
	  document.getElementById("staff").checked = false;
	  document.getElementById("reg_login").innerHTML  = "E-mail: ";
	  document.getElementById("reg_login_input").type = "email";
  } 
  function setAgent(){
    var checked = document.getElementById("agent");
    if(checked.checked)
      document.getElementById("customer_form").style.display = "none";  
	  document.getElementById("agent_form").style.display = "block";   
	  document.getElementById("staff_form").style.display = "none";
	  document.getElementById("customer").checked = false;
	  document.getElementById("staff").checked = false;
	  document.getElementById("reg_login").innerHTML  = "E-mail: ";
	  document.getElementById("reg_login_input").type = "email";
  } 
  function setStaff(){
    var checked = document.getElementById("staff");
    if(checked.checked)
      document.getElementById("customer_form").style.display = "none";  
	  document.getElementById("agent_form").style.display = "none";  
	  document.getElementById("staff_form").style.display = "block"; 
	  document.getElementById("customer").checked = false;
	  document.getElementById("agent").checked = false;	  
	  document.getElementById("reg_login").innerHTML  = "Username: ";
	  document.getElementById("reg_login_input").type = "text";
  } 
</script>
<?php
include("header.php");
$con = connect();
$error = "";
function state_selector(){
	echo '<select name="state" size="1">
  <option value="AK">AK</option>
  <option value="AL">AL</option>
  <option value="AR">AR</option>
  <option value="AZ">AZ</option>
  <option value="CA">CA</option>
  <option value="CO">CO</option>
  <option value="CT">CT</option>
  <option value="DC">DC</option>
  <option value="DE">DE</option>
  <option value="FL">FL</option>
  <option value="GA">GA</option>
  <option value="HI">HI</option>
  <option value="IA">IA</option>
  <option value="ID">ID</option>
  <option value="IL">IL</option>
  <option value="IN">IN</option>
  <option value="KS">KS</option>
  <option value="KY">KY</option>
  <option value="LA">LA</option>
  <option value="MA">MA</option>
  <option value="MD">MD</option>
  <option value="ME">ME</option>
  <option value="MI">MI</option>
  <option value="MN">MN</option>
  <option value="MO">MO</option>
  <option value="MS">MS</option>
  <option value="MT">MT</option>
  <option value="NC">NC</option>
  <option value="ND">ND</option>
  <option value="NE">NE</option>
  <option value="NH">NH</option>
  <option value="NJ">NJ</option>
  <option value="NM">NM</option>
  <option value="NV">NV</option>
  <option value="NY">NY</option>
  <option value="OH">OH</option>
  <option value="OK">OK</option>
  <option value="OR">OR</option>
  <option value="PA">PA</option>
  <option value="RI">RI</option>
  <option value="SC">SC</option>
  <option value="SD">SD</option>
  <option value="TN">TN</option>
  <option value="TX">TX</option>
  <option value="UT">UT</option>
  <option value="VA">VA</option>
  <option value="VT">VT</option>
  <option value="WA">WA</option>
  <option value="WI">WI</option>
  <option value="WV">WV</option>
  <option value="WY">WY</option>
</select>';
};

function airline_selector()
{
	$con = connect();
	$query = $con->query("SELECT airline_name FROM airline");
	$airlines = $query->fetch_all();
	echo '<select name="airline" size="1">';
	for ($var = 0;$var < count($airlines);$var++)
	{
		$current_row = $airlines[$var];
		echo "<option value='$current_row[0]'>$current_row[0]</option>";
	}
	echo '</select>';
	disconnect($con);
}
if(isset($_POST["register"]))
{
	if($_POST["password"] === $_POST["password_confirm"])
	{
		$password = md5($_POST["password"]);
	}
	else{$error = "</br>Your passwords do not match";}
	if(!isset($_POST["reg_login_input"]))
	{
		$error = "Please enter email/username";
	}
	if($query = $con->prepare("SELECT username FROM Person WHERE username=?"))
	{
	$query->bind_param('s', mysqli_real_escape_string($con,$_POST["reg_login_input"]));
	$query->execute();
	$results = $query->get_result();
	$login_exist = $results->fetch_all();
	}
	if(count($login_exist) != 0)
	{
		$error = "</br>This username is already in use";
	}
	if($error == "")
	{
		if($_POST["account_type"] == "customer")
		{
			$query = $con->prepare("INSERT INTO customer (email,name,password,building_number,street,city,state,phone_number,passport_number,passport_expiration,passport_country,date_of_birth)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?);");
			$query->bind_param('sssssssissis', mysqli_real_escape_string($con,$_POST["reg_login_input"]),
			mysqli_real_escape_string($con,$_POST["name"]),
			$password,
			mysqli_real_escape_string($con,$_POST["building_number"]),
			mysqli_real_escape_string($con,$_POST["street"]),
			mysqli_real_escape_string($con,$_POST["city"]),
			mysqli_real_escape_string($con,$_POST["state"]),
			mysqli_real_escape_string($con,$_POST["phone_number"]),
			mysqli_real_escape_string($con,$_POST["passport_number"]),
			mysqli_real_escape_string($con,$_POST["passport_expiration"]),
			mysqli_real_escape_string($con,$_POST["passport_country"]),
			mysqli_real_escape_string($con,$_POST["dob"]));
			$query->execute();
		}
		else if($_POST["account_type"] == "agent")
		{
			$query = $con->prepare("INSERT INTO booking_agent(email,password,booking_agent_id)
			VALUES (?,?,?);");
			$query->bind_param('ssi', mysqli_real_escape_string($con,$_POST["reg_login_input"]),
			$password,
			mysqli_real_escape_string($con,$_POST["booking_agent_id"]));
			$query->execute();
		}
		else if($_POST["account_type"] == "staff")
		{	
			$query = $con->prepare("INSERT INTO airline_staff (username,password,first_name,last_name,date_of_birth,airline_name)
			VALUES (?,?,?,?,?,?);");
			$query->bind_param('ssssss', mysqli_real_escape_string($con,$_POST["reg_login_input"]),
			$password,
			mysqli_real_escape_string($con,$_POST["first_name"]),
			mysqli_real_escape_string($con,$_POST["last_name"]),
			mysqli_real_escape_string($con,$_POST["dob"]),
			mysqli_real_escape_string($con,$_POST["airline"]));
			$query->execute();
		}
		$query = $con->prepare("INSERT INTO person(username,password,account_type)
		VALUES (?,?,?);");
		$query->bind_param('sss', mysqli_real_escape_string($con,$_POST["reg_login_input"]),
		$password,
		$_POST["account_type"]);
		$query->execute();
	}
}
disconnect($con);
?>
<form id="register_form" action="register.php" method="post">\
	<div id="account_type">
		<input type="radio" id="customer" name="account_type" value="customer" onchange="setCustomer();" checked="checked"> Customer
		<input type="radio" id="agent" name="account_type" value="agent" onchange="setAgent();"> Booking Agent
		<input type="radio" id="staff" name="account_type" value="staff" onchange="setStaff();"> Airline Staff
	</div></br></br>
	<div id="reg_form">
	<span id="reg_login">E-mail:</span> <input type="email" name="reg_login_input" id="reg_login_input"></br>
	Password: <input type="password" name="password">
	Password confirm: <input type="password" name="password_confirm"></br>
	</div>
	<div id="customer_form">
		Name: <input type="text" name="name"></br>
		Building Number: <input type="text" name="building_number">
		Street: <input type="text" name="street"></br>
		City: <input type="text" name="city">
		State: <?php state_selector(); ?></br>
		Phone Number: <input type="tel" name="phone_number"></br>
		Passport Number: <input type="text" name="passport_number">
		Passport Expiration: <input type="date" name="passport_expiration"></br>
		Passport Country: <input type="text" name="passport_country"></br>
		Date of Birth: <input type="date" name="dob">	
	</div>
	<div id="agent_form">
		Agent ID Number: <input type="number" name="booking_agent_id" min="1000000000" max="99999999999"></br>
	</div>
	<div id="staff_form">
		First Name: <input type="text" name="first_name">
		Last Name: <input type="text" name="last_name"></br>
		Date of Birth: <input type="date" name="dob">	
		Airline: <?php airline_selector(); ?></br>
	</div>
	</br>
	<input type="submit" name="register" value="Register"/>
	<?php echo $error;?>
</form>
<?php
include("footer.php");
?>