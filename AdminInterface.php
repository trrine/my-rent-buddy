<?php
session_start();
require_once("classes/AdminSession.php");

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
$session->setUserDetails($_SESSION["userID"]); // hmmmm

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
				<td><a href="ListAllCars.php" class="button">All Cars</a></td>
				<td><a href="ListAvailableCars.php" class="button">Available Cars</a></td>
				<td><a href="ListRentedCars.php" class="button">Rented Cars</a></td>
			</tr>
			<tr>
				<td><a href="InsertCar.php" class="button">Insert a Car</a></td>
				<td><a href="SearchForCar.php" class="button">Search for Car</a></td>
				<td><a href="ChangeCarStatus.php" class="button">Change Car Status</a></td>
			</tr>
		</table>
		<a href="Login.php" class="logout">LOGOUT</a>
	</body>
</html>