<?php
session_start();
require_once("classes/AdminSession.php");

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

if ($_SESSION["type"] != "admin") {
	// go to login page
    header("Location: Login.php");
	exit();
}

$session = new AdminSession();
$session->setUserDetails($_SESSION["userID"]);

$carNo = "";

if (isset($_GET["carNo"]))
	$carNo = $_GET["carNo"];

if (isset($_POST["submit"])) {

	if (!formComplete())
		$body .= "<p>Incomplete form. Please fill out all fields.</p>";

	else {
		$carNo = trim(stripslashes($_POST["carNo"]));

		if (is_numeric($carNo)) {
			$status = $_POST["status"];
			$body .= $session->updateCarStatus($carNo, $status);
			$carNo = "";
		}

		else 
			$body .= "<p>Car number must be a number</p>";
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
			<h2>Change Car Status</h2>
			<form action="ChangeCarStatus.php" method="post">
				<label>Car Number</label>
				<input type="text" name="carNo" value="<?php echo $carNo;?>" required/><br/>
				<label>New Status</label>
				Available: <input type="radio" name="status" value="available" required/> 
				Unavailable: <input type="radio" name="status" value="unavailable" required/><br/><br/> 
				<input type="submit" name="submit" value="UPDATE" /><br/>
				<div class="messageReturn"><?php echo $body; ?></div>
			</form>
		</div>
		<a href="Login.php" class="logout">LOGOUT</a>
		<a href="AdminInterface.php" class="return">GO BACK</a>
	</body>
</html>