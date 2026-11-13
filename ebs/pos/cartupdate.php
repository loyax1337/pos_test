<?php
session_start();
if(isset($_SESSION['id'])) {
	$lccd_user = $_SESSION['id'];
	$lcrefID = base64_decode($_SESSION['refID']);
	if(isset($_POST['txtdisc'])) {
		include_once 'includes/server.php';
		$lndisc = (empty($_POST['txtdisc']) ? 0.00 : ($_POST['txtdisc'] <= 0 ? 0.00 : $_POST['txtdisc']));

	    date_default_timezone_set('Asia/Manila');
    	$lddt_stamp = date('Y-m-d');
		$ltt_stamp = date('Y-m-d H:i:s');
		$itemSql = "SELECT a.cd_item, a.cd_model, a.cd_rom, a.cd_categ,  b.uprc FROM r_item a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit WHERE a.cd_item='$lcrefID'";
		$itemResult = mysqli_query($conn, $itemSql);
		$itemResultCheck = mysqli_num_rows($itemResult);
		if($itemResultCheck > 0) {
			$itemRow = mysqli_fetch_assoc($itemResult);
			$lccd_item = $itemRow['cd_item'];
			$lccd_model = $itemRow['cd_model'];
			$lccd_rom = $itemRow['cd_rom'];
			$lccd_categ = $itemRow['cd_categ'];
			$lnuprc = $itemRow['uprc'];

			$escaSql = "SELECT cd_esca, uprc FROM r_esca WHERE cd_model='$lccd_model' AND cd_rom='$lccd_rom' AND cd_categ='$lccd_categ' AND '$lddt_stamp' BETWEEN dt_start AND dt_end";
			$escaResult = mysqli_query($conn, $escaSql);
			$escaResultCheck = mysqli_num_rows($escaResult);
			if($escaResultCheck > 0) {
				$escaRow = mysqli_fetch_assoc($escaResult);
				$lccd_esca = $escaRow['cd_esca'];
				$lnuprc = $escaRow['uprc'];
			}
			
			$cartSql = "SELECT cd_user, cd_item, qty FROM t_cart WHERE cd_user='$lccd_user' AND cd_item='$lccd_item'";
			$cartResult = mysqli_query($conn, $cartSql);
			$cartResultCheck = mysqli_num_rows($cartResult);
			if($cartResultCheck > 0) {
				$cartRow = mysqli_fetch_assoc($cartResult);
				$updateCartItem = "UPDATE t_cart SET uprc='$lnuprc', disc='$lndisc', t_add='$ltt_stamp' WHERE cd_user='$lccd_user' AND cd_item='$lccd_item'";
				mysqli_query($conn, $updateCartItem);
			}
		} else {
			echo "Item not found.";
		}
		mysqli_close($conn);
	} else {
		echo "Invalid URL Parameters.";
	}
} else {
	echo "Session Expired. Please login again.";
}
?>