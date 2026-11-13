<?php
session_start();
if(isset($_SESSION['id'])) {
	$lccd_group = $_SESSION['group'];
	include_once 'includes/server.php';
	$groupSql = "SELECT a.url FROM r_group a WHERE a.cd_group='$lccd_group'";
	$groupResult = mysqli_query($conn, $groupSql);
	$groupResultCheck = mysqli_num_rows($groupResult);
	if($groupResultCheck > 0) {
		$groupRow = mysqli_fetch_assoc($groupResult);
		$lcurl = $groupRow['url'];
		header("Location: ".$lcurl."index.php");
	} else {
		session_destroy();
		header("Location: ../login.php");
	}
	mysqli_close($conn);
} else {
	header("Location: ../login.php");
}

?>