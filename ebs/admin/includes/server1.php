<?php
	$dbServername ="localhost:3306";
	$dbUsername ="klpos";
	$dbPassword ="klpos@dmin123";
	$dbName = "klcollections";
	$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
	if(!$conn){
	    echo 'Database connection lost!';
	}
?>