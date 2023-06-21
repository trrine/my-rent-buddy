<?php
require_once("UserSession.php");

class AdminSession extends UserSession {

	public function insertCar($plate, $model, $type, $dailyRate, $lateRate) {
		$insert = "INSERT INTO CAR(plate, model, type, status, dailyRate, lateRate)"
			. "VALUES('" . $plate . "', '" . $model . "', '" . $type . "', '" 
			. "available" . "', '" . $dailyRate . "', '" . $lateRate . "')";

		if (!$this->plateIsUnique($plate)) {
	   		try {
				mysqli_query($this->connection, $insert);
				return "<p>Car successfully inserted.</p>";

			} catch (mysqli_sql_exception $e) {
				return "<p>An error occured</p>";
			}
		}

		else 
			return "<p>Plate is not unique</p>";   		
	}

	public function listAllCars() {
		$found = 0;
		$carString = "<table><tr>";
		$sql = "SELECT * FROM CAR";
		$result = $this->connection->query($sql);

		while (($row = $result->fetch_assoc()) != NULL) {
			if ($found % 4 == 0) {
				$carString .= "</tr><tr>";
			}

			$carString .= "<td><div class='carTitle'>CAR#" . $row["carNo"] . " " . strtoupper($row["plate"]) . "</div>"
				. "<div class='carDesc'>" . ucfirst($row["model"]) . " " . ucfirst($row["type"]) . "</div>"
				. "<div class='daily'>Daily Rate: $" . $row["dailyRate"] . "</div>"
				. "<div class='late'>Late Rate: $" . $row["lateRate"] . "</div>"
				. "<div class='status'>" . strtoupper($row["status"]) . "</div>"
				. "<a href='../project/ChangeCarStatus.php?carNo=" . $row["carNo"] 
				. "'" . " class='linkButton'>UPDATE</a></td>";
			
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

	public function listRentedCars() {
		$found = 0;
		$carString = "<table><tr>";
		$sql = "SELECT * FROM CAR WHERE status='unavailable'";
		$result = $this->connection->query($sql);

		while (($row = $result->fetch_assoc()) != NULL) {
			if ($found % 4 == 0) {
				$carString .= "</tr><tr>";
			}

			$carString .= "<td><div class='carTitle'>CAR#" . $row["carNo"] . " " . strtoupper($row["plate"]) . "</div>"
				. "<div class='carDesc'>" . ucfirst($row["model"]) . " " . ucfirst($row["type"]) . "</div>"
				. "<div class='daily'>Daily Rate: $" . $row["dailyRate"] . "</div>"
				. "<div class='late'>Late Rate: $" . $row["lateRate"] . "</div>"
				. "<a href='../project/ChangeCarStatus.php?carNo=" . $row["carNo"] 
				. "'" . " class='linkButton'>UPDATE</a></td>";
			
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

	private function plateIsUnique($plate) {
		$sql = "SELECT 1 FROM CAR WHERE plate='" . $plate . "'";
		$result = $this->connection->query($sql);
		return (mysqli_num_rows($result) != 0);
	}
}