<?php
session_start();
require_once("classes/RenterSession.php");
$body = "";

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
$body .= $session->listPreviousRentals();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>My Rent Buddy</title>
		<link rel="stylesheet" href="style/List.css">
	</head>
	<body>
		<h1>My Rent Buddy</h1>
		<h2>Your Previous Rentals</h2>
		<?php echo $body; ?>
		<a href="Login.php" class="logout">LOGOUT</a>
		<a href="RenterInterface.php" class="return">GO BACK</a>
	</body>
</html>