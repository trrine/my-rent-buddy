<?php
session_start();
require_once("classes/AdminSession.php");
require_once("classes/RenterSession.php"); 

$body = "";
$results = "";

function inputProvided() {
	if (isset($_POST["plate"]) || isset($_POST["type"]) || isset($_POST["model"])) {
		if (!(empty(trim($_POST["plate"])) && empty(trim($_POST["type"])) && empty(trim($_POST["model"]))))
			return TRUE;

		else 
			return FALSE;
	}

	return FALSE;
}

if (!(isset($_SESSION["userID"]) && isset($_SESSION["type"])))  {
	// go to login page
    header("Location: Login.php");
	exit();
}

$session = NULL;
$returnLink = "";

if ($_SESSION["type"] == "admin") {
	$session = new AdminSession();
	$returnLink = "AdminInterface.php";
}

elseif ($_SESSION["type"] == "renter") {
	$session = new RenterSession(); 
	$returnLink = "RenterInterface.php";
}

else {
	// go to login page
    header("Location: Login.php");
	exit();
}

$plate = "";
$type = "";
$model = "";

$session->setUserDetails($_SESSION["userID"]);

if (isset($_POST["submit"])) {
	$plate = $_POST["plate"];
	$type = $_POST["type"];
	$model = $_POST["model"];

	// check if at least one input field has been filled out
	if (inputProvided()) {
		$plate = strtolower(trim(stripslashes($plate)));
		$type = strtolower(trim(stripslashes($type)));
		$model = strtolower(trim(stripslashes($model)));
		$results = $session->searchForCar($plate, $type, $model);
	}

	else {
		$body .= "<p>Please fill out at least one input field.</p>";
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
			<h2>Search for Car</h2>
			<form action="SearchForCar.php" method="post">
				<label>Plate</label>
				<input type="text" name="plate" value="<?php echo $plate;?>" /><br/>
				<label>Type</label>
				<input type="text" name="type" value="<?php echo $type;?>" /><br/>
				<label>Model</label>
				<input type="text" name="model" value="<?php echo $model;?>" /><br/>
				<input type="submit" name="submit" value="SEARCH" /><br/>
			</form>
			<div class="messageSearch"><?php echo $body; ?></div>
		</div>
		<a href="Login.php" class="logout">LOGOUT</a>
		<a href="<?php echo $returnLink;?>" class="return">GO BACK</a>
		<div id="results"><?php echo $results; ?></div>
	</body>
</html>