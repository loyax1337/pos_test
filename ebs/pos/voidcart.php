<?php
	session_start();
	if(isset($_SESSION['id'])) {
		include_once 'includes/server.php';
		$lccd_user = $_SESSION['id'];

		$sql = "DELETE FROM t_cart WHERE cd_user='$lccd_user'";
		mysqli_query($conn,$sql);

		mysqli_close($conn);
	} else {
		echo 'Session Expired. Please login again.';
	}
?>