<?php
try {
    $connection = mysqli_connect("localhost", "root", "", "myrentbuddy");
} 

catch (mysqli_sql_exception $e) {
    die("Connection failed: " . mysqli_connect_errno(). "-" . mysqli_connect_error());
}
?>