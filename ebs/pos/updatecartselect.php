<?php
session_start();
if(isset($_SESSION['id'])) {
	$lccd_user = $_SESSION['id'];
	if(isset($_POST['refID']) && isset($_POST['chkcart_selected'])) {
		include_once'includes/server.php';
		$refID = base64_decode($_POST['refID']);
		$cart_selected = $_POST['chkcart_selected'];

		$updateCartItem = "UPDATE t_cart SET cart_selected='$cart_selected' WHERE CONCAT(cd_user, cd_item)='".$lccd_user.$refID."'";
		mysqli_query($conn, $updateCartItem);

		mysqli_close($conn);
	} else {
		echo "Invalid URL Parameters.";
	}
} else {
	echo "Session Expired. Please login again.";
}
?>