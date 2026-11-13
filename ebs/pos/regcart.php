<?php
session_start();
if(isset($_SESSION['id'])) {
	$lccd_user = $_SESSION['id'];
	if(isset($_POST['search'])) {
		include_once 'includes/server.php';
		$lcsearch = strtoupper(mysqli_real_escape_string($conn, $_POST['search']));

	    date_default_timezone_set('Asia/Manila');
    	$lddt_stamp = date('Y-m-d');
		$ltt_stamp = date('Y-m-d H:i:s');
		if(!empty($lcsearch)) {
			$itemSql = "SELECT a.cd_item, a.cd_unit, a.cd_model, a.cd_rom, a.cd_categ, IF(a.uprc = 0, b.uprc, a.uprc) AS uprc, a.status FROM r_item a LEFT JOIN r_unit b ON a.cd_unit=b.cd_unit WHERE a.cd_item='$lcsearch'";
			$itemResult = mysqli_query($conn, $itemSql);
			$itemResultCheck = mysqli_num_rows($itemResult);
			if($itemResultCheck > 0) {
				$itemRow = mysqli_fetch_assoc($itemResult);
				$lcstatus = $itemRow['status'];
				if($lcstatus == 'A') {
					$lccd_unit = $itemRow['cd_unit'];
					$lccd_item = $itemRow['cd_item'];
					$lccd_categ = $itemRow['cd_categ'];
					$lccd_model = $itemRow['cd_model'];
					$lccd_rom = $itemRow['cd_rom'];
					$lnuprc = $itemRow['uprc'];
					$lnqty_cart = 1;

					$escaSql = "SELECT cd_esca, uprc FROM r_esca WHERE cd_model='$lccd_model' AND cd_rom='$lccd_rom' AND cd_categ='$lccd_categ' AND '$lddt_stamp' BETWEEN dt_start AND dt_end";
					$escaResult = mysqli_query($conn, $escaSql);
					$escaResultCheck = mysqli_num_rows($escaResult);
					if($escaResultCheck > 0) {
						$escaRow = mysqli_fetch_assoc($escaResult);
						$lccd_esca = $escaRow['cd_esca'];
						$lnuprc = $escaRow['uprc'];
					}

					$cartSql = "SELECT cd_user, cd_item FROM t_cart WHERE cd_user='$lccd_user' AND cd_item='$lccd_item'";
					$cartResult = mysqli_query($conn, $cartSql);
					$cartResultCheck = mysqli_num_rows($cartResult);
					if($cartResultCheck > 0) {
						switch(substr($lccd_item,0,2)) {
							case 'KL':
								echo "Duplicate Item found in your cart.";
								break;
							case 'AC':
								$updateCartItem = "UPDATE t_cart SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item'";
								mysqli_query($conn, $updateCartItem);
								break;
							case 'AD':
								$updateCartItem = "UPDATE t_cart SET qty=qty+1 WHERE cd_user='$lccd_user' AND cd_item='$lccd_item'";
								mysqli_query($conn, $updateCartItem);
								break;
							default:
								break;
						}
					} else {
						$insertCartItem = "INSERT INTO t_cart(cd_user, cd_item, qty, uprc, t_add) VALUES('$lccd_user','$lccd_item','$lnqty_cart', '$lnuprc','$ltt_stamp')";
						mysqli_query($conn, $insertCartItem);
					}
				} else {
					echo "This product is currently on-hold.";
				}
			} else {
				echo "Barcode not found.";
			}
		} else {
			echo "Parameters must not be empty.";
		}
		mysqli_close($conn);
	} else {
		echo "Invalid URL Parameters.";
	}
} else {
	echo "Session Expired. Please login again.";
}
?>