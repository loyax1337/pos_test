<?php
	session_start();
	
	include_once 'includes/server.php';

	$lccd_user = $_SESSION['id'];
	$sql="DELETE FROM t_tempcolors WHERE cd_user='$lccd_user'";
    mysqli_query($conn, $sql);
    $sql="DELETE FROM t_tempcolorsd WHERE cd_user='$lccd_user'";
    mysqli_query($conn, $sql);
    mysqli_close($conn);
?>