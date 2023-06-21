<?php
session_start();
$_SESSION = array();
$errorMessage = "";
$errors = 0;

if (isset($_POST["submit"])) {
	include "database/DatabaseConnect.php";

	try {
		// check if users details match a student in the database
   		$query = "SELECT userID, type FROM USER WHERE userID='" . $_POST["userID"] 
   				. "' AND password_md5='" . md5(stripslashes($_POST["inputPassword"])) . "'";
   		$result = mysqli_query($connection, $query);

   		// if no match
   		if (mysqli_num_rows($result) == 0) {
   			$errorMessage .= "<p>Incorrect user ID/password combination</p>";
   			$errors++;
   		}

   		// if match, save userID and type in session
   		else {
   			$row = mysqli_fetch_assoc($result);
       		$_SESSION["userID"] = $row["userID"];
       		$_SESSION["type"] = $row["type"];
   		}
   	} catch (mysqli_sql_exception $e) {
   		die("There was an error with connecting to the database");
   	}

   	mysqli_free_result($result);
   	mysqli_close($connection);

   	// redirect to overview page after login based on user type
   	if ($errors == 0) {
   		if ($_SESSION["type"] == "renter") {
   			header("Location: RenterInterface.php");
			exit();
		}

	   	elseif ($_SESSION["type"] == "admin") {
	   		header("Location: AdminInterface.php");
			exit();
	   	}

	   	else {
	   		$errorMessage .= "There was an error logging into the system.";
	   	}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>My Rent Buddy</title>
		<link rel="stylesheet" href="style/Login.css">
	</head>
	<body>
		<h1>My Rent Buddy</h1>
		<div id="login">
			<h2>Login</h2>
			<form action="Login.php" method="post">
				<label>User ID</label>
				<input type="text" name="userID" maxLength="30" required/>
				<br/><br/>
				<label>Password</label>
				<input type="password" name="inputPassword" required/>
				<br/><br/>
				<input type="submit" name="submit" value="LOGIN" />
				<br/><br/>
			</form>
			<a href="Register.php">Register</a>
			<div id="errorPlaceholder"><?php echo $errorMessage;?></div>
		</div>
	</body>
</html>