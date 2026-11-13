<?php
	$dbServername ="localhost:3306";
	$dbUsername ="ebs";
	$dbPassword ="klmktg@dmin";
	$dbName = "klmktgpos";
	$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
	if(!$conn){
	    echo 'Database connection lost!';
	}
?>