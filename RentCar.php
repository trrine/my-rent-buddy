<?php
session_start();
require_once("classes/RenterSession.php");

$body = "";
$today = new DateTime("today");

// initialise input
$carNo = "";
$endDate = "";

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

if (isset($_GET["carNo"]))
	$carNo = $_GET["carNo"];

if (isset($_POST["submit"])) {
	$carNo = $_POST["carNo"];
	$endDate = $_POST["endDate"];

	if (!formComplete())
		$body .= "<p>Incomplete form. Please fill out all fields.</p>";

	else {
		$errorCount = 0;
		// TO DO: validate date
		$carNo = trim(stripslashes($carNo));
		$endDate = new DateTime($endDate);

		if ($endDate < $today) {
			$body .= "<p>End date cannot be earlier than today.</p>";
			$errorCount++;
		}


		if (!is_numeric($carNo)) {
			$body .= "<p>Car number must be a number</p>";
			$errorCount++;
		}

		if ($errorCount == 0)
			$body .= $session->rentCar($carNo, $today->format("Y-m-d"), $endDate->format("Y-m-d"));

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
			<h2>Book a Rental</h2>
			<form action="RentCar.php" method="post">
				<label>Car Number</label>
				<input type="text" name="carNo" value="<?php echo $carNo;?>" required/><br/>
				<label>Return Date</label>
				<input type="date" name="endDate" min="<?php echo $today->format("Y-m-d");?>" required/><br/>
				<input type="submit" name="submit" value="RENT" /><br/>
			</form>
			<div class="messageRent"><?php echo $body; ?></div>
		</div>
		<a href="Login.php" class="logout">LOGOUT</a>
		<a href="RenterInterface.php" class="return">GO BACK</a>
	</body>
</html>