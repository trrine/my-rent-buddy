<!DOCTYPE html>
<html>
<head>
<title>Create Database</title>
</head>
<body>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myrentbuddy";

// create connection
try {
    $connection = mysqli_connect($servername, $username, $password); 
} 

catch ( mysqli_sql_exception $e) {
    die("Connection failed:" . mysqli_connect_errno() . "-" . mysqli_connect_error());
}

// create database
$databaseQuery = "CREATE DATABASE IF NOT EXISTS $dbname";

try {
    mysqli_query($connection, $databaseQuery);  
    mysqli_select_db($connection, $dbname);
    echo "<p>Database created successfully</p>"; 
} 

catch(mysqli_sql_exception $e) {
    die("Error creating database: " . mysqli_error($connection)); 
}

// create table car
$car = "CREATE TABLE IF NOT EXISTS CAR("
    . "carNo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,"
    . "plate VARCHAR(6) UNIQUE NOT NULL,"
    . "model VARCHAR(30) NOT NULL,"
    . "type VARCHAR(30) NOT NULL,"
    . "status VARCHAR(30) NOT NULL,"
    . "dailyRate DECIMAL(6, 2) NOT NULL,"
    . "lateRate DECIMAL(6, 2) NOT NULL)";

try {
    mysqli_query($connection, $car);
    echo "<p>Table CAR created successfully</p>";
} 

catch (mysqli_sql_exception $e) {
     die("Error creating table: " . mysqli_error($connection));
}

// create table user
$user = "CREATE TABLE IF NOT EXISTS USER("
    . "userID VARCHAR(30) PRIMARY KEY,"
    . "password_md5 CHAR(32) NOT NULL,"
    . "type VARCHAR(6) NOT NULL,"
    . "name VARCHAR(30) NOT NULL,"
    . "surname VARCHAR(30) NOT NULL,"
    . "phone VARCHAR(10) NOT NULL,"
    . "email VARCHAR(50) NOT NULL)";

try {
    mysqli_query($connection, $user);
    echo "<p>Table USER created successfully</p>";
} 

catch (mysqli_sql_exception $e) {
     die("Error creating table: " . mysqli_error($connection));
}

// create table rental
$rental = "CREATE TABLE IF NOT EXISTS RENTAL("
    . "renterID VARCHAR(30) NOT NULL," // consider composite pk 
    . "carNo INT UNSIGNED NOT NULL,"
    . "startDate DATE NOT NULL,"
    . "endDate DATE NOT NULL,"
    . "dateReturned DATE)";

try {
    mysqli_query($connection, $rental);
    echo "<p>Table RENTAL created successfully</p>";
} 

catch (mysqli_sql_exception $e) {
     die("Error creating table: " . mysqli_error($connection));
}

// load tables

$carInsert = "INSERT INTO CAR(plate, model, type, status, dailyRate, lateRate) "
    . "VALUES('QW12RT', 'Audi A6', 'sedan', 'unavailable', 50, 60),"
    . "('NM24TF', 'Toyota Sienna', 'minivan', 'available', 90, 115),"
    . "('WE98MJ', 'Kia Carnival', 'minivan', 'unavailable', 95, 105),"
    . "('TH09NN', 'Audi A4', 'sedan', 'available', 68, 80),"
    . "('AB13CD', 'Hyundai Elantra XD', 'sedan', 'available', 68, 80),"
    . "('CD67GH', 'Tesla Model S', 'electric', 'available', 90, 100),"
    . "('DD56GH', 'Toyota Hiace', 'campervan', 'available', 110, 120),"
    . "('BL51BV', 'Hyundai Ioniq 5', 'electric', 'available', 75, 85),"
    . "('XS76PL', 'Peugeot 309', 'sedan', 'unavailable', 65, 70)";

$userInsert = "INSERT INTO USER(userID, password_md5, type, name, surname, phone, email) "
    . "VALUES('renter', md5('123123'), 'renter', 'alma', 'quist', '0412345678', 'alma_q@random.com'),"
    . "('admin', md5('123123'), 'admin', 'kelly', 'jones', '0487654321', 'kelly@myrentbuddy.com')";

$rentalInsert = "INSERT INTO RENTAL(renterID, carNo, startDate, endDate, dateReturned) "
    . "VALUES('renter', 1, '2023-02-12', '2023-02-15', '2023-02-15'),"
    . "('renter', 3, '2023-04-21', '2023-04-28', '2023-04-29'),"
    . "('renter', 5, '2023-04-29', '2023-05-10', '2023-05-10'),"
    . "('renter', 6, '2023-05-21', '2023-05-21', '2023-05-21'),"
    . "('renter', 1, '2023-05-11', '2023-05-29', NULL),"
    . "('renter', 3, '2023-05-21', '2023-06-05', NULL),"
    . "('renter', 9, '2023-05-23', '2023-06-21', NULL)";

try {
    mysqli_query($connection, $carInsert);
    mysqli_query($connection, $userInsert);
    mysqli_query($connection, $rentalInsert);
    echo "<p>Successfully loaded tables</p>";

} catch (mysqli_sql_exception $e) {
     die("Error loading tables");
}

mysqli_close($connection);
?>
</body>
</html>