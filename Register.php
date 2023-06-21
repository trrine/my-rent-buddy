<?php
session_start();
$_SESSION = array();
$errorMessage = "";

// initialise input
$userID = "";
$name = "";
$surname = "";
$email = "";
$phone = "";
$type = "";

function formComplete() {
   	foreach ($_POST as $key => $value) {
   		if (!isset($value) || (trim($value) == "")) {
   			return FALSE;
   		}
   	}
   	return TRUE;
}

if (isset($_POST["submit"])) {
	$errors = 0;
	$userID = $_POST["userID"];
	$name = $_POST["name"];
	$surname = $_POST["surname"];
	$email = $_POST["email"];
	$phone = $_POST["phone"];

	if (isset($_POST["type"]))
		$type = $_POST["type"];

	if (!formComplete())
		$errorMessage = "<p>Incomplete form. Please fill out all fields.</p>";
	
	else {	
		// remove beginning and trailing whitespace and backslashes
		// convert text to lower
		$userID = strtolower(trim(stripslashes($userID)));
		$name = strtolower(trim(stripslashes($name)));
		$surname = strtolower(trim(stripslashes($surname)));
		$email = strtolower(trim(stripslashes($email)));
		$phone = trim(stripslashes($phone));
		$password1 = stripslashes($_POST["password1"]);
		$password2 = stripslashes($_POST["password2"]);

		// validate userID length
		if (strlen($userID) > 30) {
			$errorMessage .= "<p>User ID exceeds 30 characters.</p>";
			$errors++;
		}

		// validate name length
		if (strlen($name) > 30) {
			$errorMessage .= "<p>Name exceeds 30 characters.</p>";
			$errors++;
		}

		// validate surname length
		if (strlen($surname) > 30) {
			$errorMessage .= "<p>Surname exceeds 30 characters.</p>";
			$errors++;
		}

		// validate password
		if (strlen($password1) < 6) {
			$errorMessage .= "<p>Password must be at least 6 characters long.</p>";
			$errors++;
		}

		if ($password1 <> $password2) {
			$errorMessage .= "<p>The entered passwords do not match.</p>";
			$errors++;
		}

		// validate email length
		if (strlen($email) > 50) {
			$errorMessage .= "<p>Email exceeds 50 characters.</p>";
			$errors++;
		}

		// validate email format
		if (!preg_match("/^[\w\-_]+(\.[\w\-_]+)*@[\w\-_]+(\.[\w\-_]+)*(\.[a-zA-Z]{2,3})/", $email)) {
			 $errorMessage .= "<p>Invalid email format.</p>";
			$errors++;
		}

		// validate phone number
		// FORMAT:
		// 04NNNNNNNN (N=number)
		if (!preg_match("/^04\d{8}/", $phone)) {
			$errorMessage .= "<p>Enter an Australian mobile number.</p>";
			$errors++;
		}
	

		if ($errors == 0) {
			$registered = FALSE;
			include "database/DatabaseConnect.php";

			// check if userID is unique
			$userCheck = "SELECT * FROM USER WHERE userID='" . $userID . "'";
			$result = mysqli_query($connection, $userCheck);

			// if match
	   		if (mysqli_num_rows($result) > 0) {
	   			$errorMessage .= "<p>User ID already exists. Please choose another.</p>";
	   		}

	   		// create user
	   		else {
	   			$insert = "INSERT INTO USER(userID, password_md5, type, name, surname, phone, email)" 
	   			. " VALUES('" . $userID . "', '" . md5($password1) . "', '" 
	   				. $type . "', '" . $name . "', '" . $surname . "', '" . $phone . "', '" . $email . "')";

	   			try {
				    mysqli_query($connection, $insert);
				    $registered	= TRUE;

				} catch (mysqli_sql_exception $e) {
				     die("Error loading table");
				}   			
	   		}

	   		mysqli_free_result($result);
	   		mysqli_close($connection);

	   		if ($registered) {
	   			// save userID and type in session
	   			$_SESSION["userID"] = $userID;
	       		$_SESSION["type"] = $type;

	       		// reset inputs
				$userID = "";
				$name = "";
				$surname = "";
				$email = "";
				$phone = "";
				$type = "";	
				$password1 = "";
				$password2 = "";	

	       		// redirect based on type
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
	}

}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>My Rent Buddy</title>
		<link rel="stylesheet" href="style/Register.css">
	</head>
	<body>
		<h1>My Rent Buddy</h1>
		<div id="register">
			<h2>Register</h2>
			<form action="register.php" method="post">
				<label>User ID</label>
				<input type="text" name="userID" value="<?php echo $userID;?>" maxLength="30" required/><br/>
				<label>First Name</label>
				<input type="text" name="name" value="<?php echo $name;?>" maxLength="30" required/><br/>
				<label>Surname</label>
				<input type="text" name="surname" value="<?php echo $surname;?>" maxLength="30" required/><br/>
				<label>Email</label>
				<input type="text" name="email" value="<?php echo $email;?>" maxLength="50" required/><br/>
				<label>Phone Number</label>
				<input type="text" name="phone" value="<?php echo $phone;?>" maxLength="10" required/><br/>
				<label>Password</label>
				<input type="password" name="password1" required/><br/>
				<label>Confirm password</label>
				<input type="password" name="password2" required/><br/>
				<label>User Type</label>
				Renter: <input type="radio" name="type" value="renter" <?php echo ($type=="renter")?"checked='checked'":"";?> required/> 
				Administrator: <input type="radio" name="type" value="admin" <?php echo ($type=="admin")?"checked='checked'":"";?> required/><br/><br/> 
				<input type="submit" name="submit" value="REGISTER" /><br/>
			</form>
			<div class="errorPlaceholder"><?php echo $errorMessage;?></div>
		</div>
		<a href="Login.php" class="logout">GO BACK</a>
	</body>
</html>