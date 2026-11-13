<?php
	session_start();
	
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
	
		include_once 'includes/server.php';

		$sql="DELETE FROM t_tempsi WHERE cd_user='$lccd_user'";
	    mysqli_query($conn, $sql);
	    $sql="DELETE FROM t_tempsid WHERE cd_user='$lccd_user'";
	    mysqli_query($conn, $sql);
	    mysqli_close($conn);
	} else {
		echo "Session Expired. Please login again.";
	}
?>