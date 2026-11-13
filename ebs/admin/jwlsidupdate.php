<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		$lccd_group = $_SESSION['group'];
		$lccd_brnch = $_SESSION['branch'];

		$lcref1 = $_SESSION['ref1'];
		$lcref2 = $_SESSION['ref2'];

		if($lccd_group == 'GS00000001' || $lccd_group == 'GS00000002') {
			if(isset($_POST['txtucst'])) {
				include_once 'includes/server1.php';
				$lnucst = (empty($_POST['txtucst']) ? 0.00 : (($_POST['txtucst'] <=0) ? 0.00 : $_POST['txtucst']) );

				if($lnucst >= 0) {
					$tranDetSql = "SELECT a.no_tran, a.cd_item, a.cd_stock, a.qty FROM t_sidjwl a WHERE a.no_tran='$lcref1' AND a.cd_item='$lcref2'";
					$tranDetResult = mysqli_query($conn, $tranDetSql);
					$tranDetResultCheck = mysqli_num_rows($tranDetResult);
					if($tranDetResultCheck > 0) {
						$tranDetRow = mysqli_fetch_assoc($tranDetResult);
						$lnqty = $tranDetRow['qty'];
						$lnamt_cst = ($lnqty * $lnucst);

						$updateTranDet = "UPDATE t_sidjwl SET ucst='$lnucst', amt_cst='$lnamt_cst' WHERE no_tran='$lcref1' AND cd_item='$lcref2'";
						mysqli_query($conn, $updateTranDet);

						unset($_SESSION['ref1']);
						unset($_SESSION['ref2']);
					} else {
						echo "Reference ID could not be found.";
					}
				} else {
					echo "Please input a valid Cost amount.";
				}
				mysqli_close($conn);
			} else {
				echo "Invalid Url Parameters.";
			}
		} else {
			echo "Access denied! Group access rights cannot perform this action.";
		}
	} else {
		echo "Session Expired. Please login again.";
	}
?>