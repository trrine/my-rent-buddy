<?php
require_once("UserSession.php");

class RenterSession extends UserSession {

	public function rentCar($carNo, $startDate, $endDate) {  
		// check if car exists and is available
		if ($this->carAvailable($carNo)) {
		    $insert = "INSERT INTO RENTAL(renterID, carNo, startDate, endDate)"
		    	. "VALUES('" . $this->ID . "', '" . $carNo . "', '" . $startDate . "', '" . $endDate . "')";
		    try {
				mysqli_query($this->connection, $insert);

				// update car status
				$this->updateCarStatus($carNo, "unavailable");
				
				$costString = "Rental saved.<br/>Length: " . $this->calculateDays($startDate, $endDate) 
					. " day(s). <br/>Return date: $endDate. <br/>Estimated cost: $"
					. $this->calculateEstimatedCost($carNo, $startDate, $endDate) . ".";

				return $costString;

			} catch (mysqli_sql_exception $e) {
				return "<p>Rental unsuccessful - an error occured</p>";
			}
		}

		else {
			return "<p>Rental unsuccessful - please choose another car</p>";
		}   		
	}

	public function returnCar($carNo) {
		// find current date 
		$today = new DateTime("today");
		$today = $today->format("Y-m-d");
		$totalCost = 0;

		// check if car is already returned
		$sql = "SELECT startDate, endDate FROM RENTAL WHERE renterID='" . $this->ID 
			. "' AND carNo='" . $carNo . "' AND dateReturned IS NULL";
		$result = $this->connection->query($sql);

		if (mysqli_num_rows($result) == 0) 
			return "<p>Car not rented.</p>";

		$row = mysqli_fetch_assoc($result);
		$startDate = $row["startDate"];
		$endDate = $row["endDate"];
		$totalCost = $this->calculateTotalCost($carNo, $startDate, $endDate, $today);			

		// update car status and rental return date in database
		$this->updateCarStatus($carNo, "available");
		return "<p>Car returned. Total cost: \$$totalCost</p>"; 
		
	}

	public function listCurrentRentals() {
		$sql = "SELECT * FROM RENTAL JOIN CAR ON RENTAL.carNo=CAR.carNo"
			. " WHERE renterID='" . $this->ID . "' AND dateReturned IS NULL";
		$result = $this->connection->query($sql);
		$rentalString = "<table><tr>";
		$found = 0;

		while (($row = $result->fetch_assoc()) != NULL) {
			if ($found % 4 == 0) {
				$rentalString .= "</tr><tr>";
			}

			$rentalString .= "<td><div class='carTitle'>CAR#" . $row["carNo"] . " " . strtoupper($row["plate"]) . "</div>"
				. "<div class='carDesc'>" . ucfirst($row["model"]) . " " . ucfirst($row["type"]) . "</div>"
				. "Daily Rate: $" . $row["dailyRate"] . "</br>"
				. "Late Rate: $" . $row["lateRate"] . "<br/>"
				. "Start Date: " . $row["startDate"] . "<br/>"
				. "End Date: " . $row["endDate"] . "<br/><br/>"
				. "<a href='../project/ReturnCar.php?carNo=" . $row["carNo"] 
						. "'" . " class='linkButton'>RETURN</a></td>";

			$found++;
		}

		if ($found == 0) {
			$carString = "";
		}

		// for formatting 
		elseif ($found%4 == 0) {
			$rentalString .= "</tr></table>";
		}

		else {
			$cells = 4 - ($found%4);
			$rentalString .= str_repeat("<td class='empty'>&nbsp;</td>", $cells);
		}
		
		return $rentalString;
	}

	public function listPreviousRentals() {
		$sql = "SELECT * FROM RENTAL JOIN CAR ON RENTAL.carNo=CAR.carNo"
			. " WHERE renterID='" . $this->ID . "' AND dateReturned IS NOT NULL";
		$result = $this->connection->query($sql);
		$rentalString = "<table><tr>";
		$found = 0;

		while (($row = $result->fetch_assoc()) != NULL) {
			if ($found % 4 == 0) {
				$rentalString .= "</tr><tr>";
			}

			$rentalString .= "<td><div class='carTitle'>CAR#" . $row["carNo"] . " " . strtoupper($row["plate"]) . "</div>"
				. "<div class='carDesc'>" . ucfirst($row["model"]) . " " . ucfirst($row["type"]) . "</div>"
				. "Daily Rate: $" . $row["dailyRate"] . "</br>"
				. "Late Rate: $" . $row["lateRate"] . "<br/>"
				. "Start Date: " . $row["startDate"] . "<br/>"
				. "End Date: " . $row["endDate"] . "</td>";

			$found++;
		}

		if ($found == 0) {
			$carString = "";
		}

		// for formatting 
		elseif ($found%4 == 0) {
			$rentalString .= "</tr></table>";
		}

		else {
			$cells = 4 - ($found%4);
			$rentalString .= str_repeat("<td class='empty'>&nbsp;</td>", $cells);
		}
		
		return $rentalString;
	}

	private function calculateEstimatedCost($carNo, $startDate, $endDate) {
		$dailyRate = 0;
		$cost = 0;
		$start = new DateTime($startDate);
		$end = new DateTime($endDate);
		$days = $this->calculateDays($startDate, $endDate);

		// find daily rate for car
		$sql = "SELECT dailyRate FROM CAR WHERE carNo='" . $carNo . "'";
		$result = $this->connection->query($sql);

		while (($row = $result->fetch_assoc()) != NULL) {
			$dailyRate = $row["dailyRate"];
			
		}

		return $dailyRate * $days;
	}

	private function calculateTotalCost($carNo, $startDate, $endDate, $returnDate) { // consider what happens if early return
		$dailyRate = 0;
		$lateRate = 0;
		$cost = 0;
		$days = 0;
		$lateDays = 0;
		$start = new DateTime($startDate);
		$end = new DateTime($endDate);
		$return = new DateTime($returnDate);

		// if the renter returns the car earlier or on time
		if ($returnDate <= $endDate) 
			$days = $this->calculateDays($startDate, $returnDate);
		
		else {
			$days = $this->calculateDays($startDate, $endDate);
			$lateDays = $this->calculateDays($endDate, $returnDate);
		}

		// find daily rate for car
		$sql = "SELECT dailyRate, lateRate FROM CAR WHERE carNo='" . $carNo . "'";
		$result = $this->connection->query($sql);

		while (($row = $result->fetch_assoc()) != NULL) {
			$dailyRate = $row["dailyRate"];
			$lateRate = $row["lateRate"];
			
		}

		return $dailyRate * $days + $lateRate * $lateDays;
	}

	private function calculateDays($start, $end) {
		$start = new DateTime($start);
		$end = new DateTime($end);
		$days = $start->diff($end);
		return $days->d + 1; // inclusive
	}

	private function carReturned($carNo) {
		$sql = "SELECT startDate, endDate FROM CAR WHERE userID='" 
			. $this->ID . " AND carNo='" . $carNo 
			. "' AND dateReturned IS NOT NULL";
		$result = $this->connection->query($sql);
		return (mysqli_num_rows($result) != 0);
	}
}
?>