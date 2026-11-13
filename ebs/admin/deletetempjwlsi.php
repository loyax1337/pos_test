<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
	
		include_once 'includes/server1.php';

		$sql="DELETE FROM t_tempsijwl WHERE cd_user='$lccd_user'";
	    mysqli_query($conn, $sql);
	    $sql="DELETE FROM t_tempsidjwl WHERE cd_user='$lccd_user'";
	    mysqli_query($conn, $sql);
	    mysqli_close($conn);
	} else {
		echo "Session Expired. Please login again.";
	}
?>