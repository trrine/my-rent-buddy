<?php
session_start();
require_once("classes/RenterSession.php");

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

?>
<!DOCTYPE html>
<html>
	<head>
		<title>My Rent Buddy</title>
		<link rel="stylesheet" href="style/Interface.css">
	</head>
	<body>
		<h1>My Rent Buddy</h1>
		<h2><?php echo "Hello, " . ucfirst($session->getName());?></h2>
		<table>
			<tr>
				<td><a href="RentCar.php" class="button">Rent a Car</a></td>
				<td><a href="ReturnCar.php" class="button">Return a Car</a></td>
				<td><a href="SearchForCar.php" class="button">Search for Car</a></td>
			</tr>
			<tr>
				<td><a href="ListCurrentUserRentals.php" class="button">Your Current Rentals</a></td>
				<td><a href="ListPreviousUserRentals.php" class="button">Your Previous Rentals</a></td>
				<td><a href="ListAvailableCars.php" class="button">Available Cars</a></td>
			</tr>
		</table>
		<a href="Login.php" class="logout">LOGOUT</a></h4>
	</body>
</html>