<?php
session_start();
require_once("classes/AdminSession.php");

$body = "";

// initialise input
$plate = "";
$model = "";
$type = "";
$dailyRate = "";
$lateRate = "";

function formComplete() {
   	foreach ($_POST as $key => $value) {
   		if (!isset($value) || empty(trim($value))) {
   			return FALSE;
   		}
   	}
   	return TRUE;
}

function validateRate($rate) {
	if (!is_numeric($rate))
		return FALSE; 

	if ($rate <= 0 || $rate > 9999.99)
		return FALSE; 

	return TRUE;
}

if (!(isset($_SESSION["userID"]) && isset($_SESSION["type"])))  {
	// go to login page
    header("Location: Login.php");
	exit();
}

if ($_SESSION["type"] != "admin") {
	// go to login page
    header("Location: Login.php");
	exit();
}

$session = new AdminSession();
$session->setUserDetails($_SESSION["userID"]);

if (isset($_POST["submit"])) {
	$plate = $_POST["plate"];
	$model = $_POST["model"];
	$type = $_POST["type"];
	$dailyRate = $_POST["dailyRate"];
	$lateRate = $_POST["lateRate"];

	if (!formComplete()) 
		$body = "<p>Incomplete form. Please fill out all fields.</p>";

	else {
		$errorCount = 0;

		// remove slashes and whitespace 
		$plate = strtoupper(trim(stripslashes($plate)));
		$model = strtolower(trim(stripslashes($model)));
		$type = strtolower(trim(stripslashes($type)));
		$dailyRate = trim(stripslashes($dailyRate));
		$lateRate = trim(stripslashes($lateRate));

		// validate input

		// valid plate pattern: AANNAA 
		// A=letter, N=number
		if (!preg_match("/[A-Z]{2}\d{2}[A-Z]{2}/", $plate)) {
			$body .= "<p>Plate must be in format: AANNAA (A=letter, N=number).</p>";
			$errorCount++;
		}

		// check if rates are valid
		if (!validateRate($dailyRate) || !validateRate($lateRate)) {
			$body .= "<p>Rates must be between 0.01 and 9999.99.</p>";
			$errorCount++;
		}

		// validate model length
		if (strlen($model) > 30) {
			$body .= "<p>Max length of model is 30.</p>";
			$errorCount++;
		}

		// validate type length
		if (strlen($type) > 30) {
			$body .= "<p>Max length of type is 30.</p>";
			$errorCount++;
		}

		if ($errorCount == 0) {
			$body .= $session->insertCar($plate, $model, $type, $dailyRate, $lateRate);
		}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
	<title>My Rent Buddy</title>
	<link rel="stylesheet" href="style/Insert.css">
</head>
	<body>
		<h1>My Rent Buddy</h1>
		<div id="insert">
			<h2>Insert Car</h2>
			<form action="InsertCar.php" method="post">
				<label>Plate</label>
				<input type="text" name="plate" value="<?php echo $plate; ?>" maxLength="6" required/><br/>
				<label>Model</label>
				<input type="text" name="model" value="<?php echo $model; ?>" maxLength="30" required/><br/>
				<label>Type</label>
				<input type="text" name="type" value="<?php echo $type; ?>" maxLength="30" required/><br/>
				<label>Daily Rate</label>
				<input type="text" name="dailyRate" value="<?php echo $dailyRate; ?>" required/><br/>
				<label>Late Rate</label>
				<input type="text" name="lateRate" value="<?php echo $lateRate; ?>" required/><br/>
				<input type="submit" name="submit" value="INSERT" /><br/>
			</form>
			<div id="errorPlaceholder"><?php echo $body; ?></div>
		</div>
		<a href="Login.php" class="logout">LOGOUT</a>
		<a href="AdminInterface.php" class="return">GO BACK</a>
	</body>
</html>