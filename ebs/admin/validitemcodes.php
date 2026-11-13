<?php
session_start();

if(isset($_SESSION['id'])) {
	$lccd_user = $_SESSION['id'];

	if(isset($_POST['txtcd_item1']) && isset($_POST['txtcd_item2'])) {
		include_once 'includes/server.php';
		$lncd_item1 = (empty($_POST['txtcd_item1']) ? 0 : (($_POST['txtcd_item1'] <= 0) ? 0 : $_POST['txtcd_item1']));
		$lncd_item2 = (empty($_POST['txtcd_item2']) ? 0 : (($_POST['txtcd_item2'] <= 0) ? 0 : $_POST['txtcd_item2']));

		if($lncd_item1 > 0) {
			if($lncd_item2 > 0) {
				if($lncd_item1 <= $lncd_item2) {
					$lccd_item1 = 'KL'.str_pad($lncd_item1,9,'0',STR_PAD_LEFT);
					$lccd_item2 = 'KL'.str_pad($lncd_item2,9,'0',STR_PAD_LEFT);
					$itemSql = "SELECT cd_item FROM r_item WHERE cd_item='$lccd_item1' OR cd_item='$lccd_item2'";
					$itemResult = mysqli_query($conn, $itemSql);
					$itemResultCheck = mysqli_num_rows($itemResult);
					if($itemResultCheck < 2) {
						echo "Either the start or end code does not exist.";
					}
				} else {
					echo "End code must not be greater than Start code.";
				}
			} else {
				echo "Please enter a valid End code.";
			}
		} else {
			echo "Please enter a valid Start code.";
		}
	} else {
		echo "Invalid Url Parameters.";
	}
} else {
	echo "Session Expired. Please log in again.";
}
?>