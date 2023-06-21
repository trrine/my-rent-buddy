<?php
class UserSession {
	protected $connection;
	protected $ID;
	private $name;
	private $surname;
	private $phone;
	private $email;
	private $type;

	public function __construct() {
		// connect to database
		include("../project/database/DatabaseConnect.php");
		$this->connection = $connection;

		// initialise variables
		$this->ID = "";
		$this->name = "";
		$this->surname = "";
		$this->phone = "";
		$this->email = "";
		$this->type = "";
	}

	public function setUserDetails($ID) {
		// set user ID
		if ($this->ID != $ID) {
			$this->ID = $ID;

			// get user details
			try {
				$sql = "SELECT userID, name, surname, phone, email, type "
					. "FROM USER WHERE userID='" . $this->ID . "'";
				$result = $this->connection->query($sql);

				while (($row = $result->fetch_assoc()) != NULL) {
					$this->name = $row["name"];
					$this->surname = $row["surname"];
					$this->phone = $row["phone"];
					$this->email = $row["email"];
					$this->type = $row["type"];
				}
			}

			// if error, reset user ID
			catch (mysqli_sql_exception $e) {
				$this->ID = "";
			}

			mysqli_free_result($result);
		}
	}

	public function getName() {
		return $this->name;
	}

	public function getID() {
		return $this->ID;
	}

	public function listAvailableCars() {
		$found = 0;
		$carString = "<table><tr>";
		$sql = "SELECT * FROM CAR WHERE status='available'";
		$result = $this->connection->query($sql);

		while (($row = $result->fetch_assoc()) != NULL) {
			if ($found % 4 == 0) {
				$carString .= "</tr><tr>";
			}

			$carString .= "<td><div class='carTitle'>CAR#" . $row["carNo"] . " " . strtoupper($row["plate"]) . "</div>"
				. "<div class='carDesc'>" . ucfirst($row["model"]) . " " . ucfirst($row["type"]) . "</div>"
				. "<div class='daily'>Daily Rate: $" . $row["dailyRate"] . "</div>"
				. "<div class='late'>Late Rate: $" . $row["lateRate"] . "</div><br/>";

				if ($this->type == "renter") {
					$carString .= "<a href='../project/RentCar.php?carNo=" . $row["carNo"] 
						. "'" . " class='linkButton'>RENT</a></td>";
				}

				else {
					$carString .= "<a href='../project/ChangeCarStatus.php?carNo=" . $row["carNo"] 
						. "'" . " class='linkButton'>UPDATE</a></td>";
				}
			$found++;
		}

		if ($found == 0) {
			$carString = "";
		}

		// for formatting 
		elseif ($found%4 == 0) {
			$carString .= "</tr></table>";
		}

		else {
			$cells = 4 - ($found%4);
			$carString .= str_repeat("<td class='empty'>&nbsp;</td>", $cells);
		}

		return $carString;
	}

	protected function carExists($carNo) { 
		$sql = "SELECT 1 FROM CAR WHERE carNo='" . $carNo . "'";
		$result = $this->connection->query($sql);
		return (mysqli_num_rows($result) != 0);
	}

	protected function carAvailable($carNo) {
		$sql = "SELECT 1 FROM CAR WHERE carNo='" . $carNo . "' AND status='available'";
		$result = $this->connection->query($sql);
		return (mysqli_num_rows($result) != 0);
	}

	public function updateCarStatus($carNo, $status) {
		$status = strtolower($status);
		$today = new DateTime("today");

		// check if car exists 
		if ($this->carExists($carNo))  {
			// update in database
			$updateCar = "UPDATE CAR SET status='" . $status . "' WHERE carNo='" 
			. $carNo . "'";
			$updateRental = "UPDATE RENTAL SET dateReturned='" . $today->format("Y-m-d") . "' WHERE carNo='" 
			. $carNo . "' AND dateReturned IS NULL";

			try {
				mysqli_query($this->connection, $updateCar);

				if ($status == "available") {
					mysqli_query($this->connection, $updateRental);
				}

				return "Car status updated.";
			}

			catch (mysqli_sql_exception $e) {
				die("Error updating table.");
			}
		}

		else {
			return "Car does not exist.";
		}
	}

	public function searchForCar($plate, $type, $model) {
		$found = 0;
		$carString = "<h3>Results</h3><table><tr>";
		$sql = "SELECT * FROM CAR WHERE"
			. " plate LIKE '%" . $plate . "%' AND"
			. " type LIKE '%" . $type . "%' AND"
			. " model LIKE '%" . $model . "%'";

		$result = $this->connection->query($sql);

		while (($row = $result->fetch_assoc()) != NULL) {
			if ($found % 4 == 0) {
				$carString .= "</tr><tr>";
			}

			$carString .= "<td><div class='carTitle'>CAR#" . $row["carNo"] . " " . strtoupper($row["plate"]) . "</div>"
				. "<div class='carDesc'>" . ucfirst($row["model"]) . " " . ucfirst($row["type"]) . "</div>"
				. "<div class='daily'>Daily Rate: $" . $row["dailyRate"] . "</div>"
				. "<div class='late'>Late Rate: $" . $row["lateRate"] . "</div>"
				. "<div class='status'>" . strtoupper($row["status"]) . "</div>";

				if ($this->type == "renter") {
					if ($row["status"] == "available") {
					$carString .= "<a href='../project/RentCar.php?carNo=" . $row["carNo"] 
						. "'" . " class='linkButton'>RENT</a></td>";
					}

					else {
						$carString .= "</td>";
					}
				}

				else {
					$carString .= "<a href='../project/ChangeCarStatus.php?carNo=" . $row["carNo"] 
						. "'" . " class='linkButton'>UPDATE</a></td>";
				}
			$found++;
		}

		if ($found == 0) {
			$carString = "<h3>No Results</h3>";
		}

		// for formatting 
		elseif ($found%4 == 0) {
			$carString .= "</tr></table>";
		}

		else {
			$cells = 4 - ($found%4);
			$carString .= str_repeat("<td class='empty'>&nbsp;</td>", $cells);
		}

		return $carString;
	}
}
?>