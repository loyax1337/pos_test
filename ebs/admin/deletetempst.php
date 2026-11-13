<?php
	session_start();
	
	include_once 'includes/server.php';

	$lccd_user = $_SESSION['id'];
	$sql="DELETE FROM t_tempst WHERE cd_user='$lccd_user' AND ty_tran='KL'";
    mysqli_query($conn, $sql);
    $sql="DELETE FROM t_tempstd WHERE cd_user='$lccd_user' AND ty_tran='KL'";
    mysqli_query($conn, $sql);
    mysqli_close($conn);
?>