<?php
session_start();
require_once("classes/RenterSession.php");

$body = "";

function formComplete() {
   	foreach ($_POST as $key => $value) {
   		if (!isset($value) || empty(trim($value))) {
   			return FALSE;
   		}
   	}
   	return TRUE;
}

if (!(isset($_SESSION["userID"]) && isset($_SESSION["type"])))  {
	// go to login page
    header("Location: Login.php");
	exit();
}

if ($_SESSION["type"] != "renter") {
	// go to login page
    header("Location: Login.php");
	exit();
}

$session = new RenterSession();
$session->setUserDetails($_SESSION["userID"]);

$carNo = "";

if (isset($_GET["carNo"]))
	$carNo = $_GET["carNo"];

if (isset($_POST["submit"])) {

	if (!formComplete())
		$body .= "<p>Incomplete form. Please fill out all fields.</p>";

	else {
		$carNo = trim(stripslashes($_POST["carNo"]));

		if (!is_numeric($carNo)) 
			$body .= "<p>Car number must be a number</p>";
			
		else {
			$body .= $session->returnCar($carNo);
			$carNo = "";
		}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
	<title>My Rent Buddy</title>
	<link rel="stylesheet" href="style/Rent.css">
</head>
	<body>
		<h1>My Rent Buddy</h1>
		<div class="form">
			<h2>Return Car</h2>
			<form action="ReturnCar.php" method="post">
				<label>Car Number</label>
				<input type="text" name="carNo" value="<?php echo $carNo;?>" required/><br/>
				<input type="submit" name="submit" value="RETURN" /><br/>
				<div class="messageReturn"><?php echo $body; ?></div>
			</form>
		</div>
		<a href="Login.php" class="logout">LOGOUT</a>
		<a href="RenterInterface.php" class="return">GO BACK</a>
	</body>
</html>