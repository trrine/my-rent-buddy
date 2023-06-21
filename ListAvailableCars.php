<?php
session_start();
require_once("classes/AdminSession.php");
require_once("classes/RenterSession.php"); 
$body = "";

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

$session->setUserDetails($_SESSION["userID"]);
$body .= $session->listAvailableCars();
?>
<!DOCTYPE html>
<html>
	<head>
	<title>My Rent Buddy</title>
	<link rel="stylesheet" href="style/List.css">
	</head>
	<body>
		<h1>My Rent Buddy</h1>
		<h2>Available Cars</h2>
		<?php echo $body; ?>
		<a href="Login.php" class="logout">LOGOUT</a>
		<a href="<?php echo $returnLink;?>" class="return">GO BACK</a>
	</body>
</html>