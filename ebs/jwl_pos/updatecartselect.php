<?php
session_start();
if(isset($_SESSION['id'])) {
	$lccd_user = $_SESSION['id'];
	if(isset($_POST['refID'])) {
		include_once'includes/server.php';
		$refID = base64_decode($_POST['refID']);
		$cart_selected = 1;

		$updateCartItem = "UPDATE t_cartjwl SET cart_selected = 1";
		mysqli_query($conn, $updateCartItem);

		mysqli_close($conn);
	} else {
		echo "Invalid URL Parameters.";
	}
} else {
	echo "Session Expired. Please login again.";
}
?>