<?php
	session_start();
	 
	include_once 'includes/server.php';

	$lccd_user = $_SESSION['id'];
	$sql="DELETE FROM t_tempsr WHERE cd_user='$lccd_user'";
    mysqli_query($conn, $sql);
    $sql="DELETE FROM t_tempsrd WHERE cd_user='$lccd_user'";
    mysqli_query($conn, $sql);
    mysqli_close($conn);
?>