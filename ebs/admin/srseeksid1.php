<?php
	session_start();

	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		$lccd_brnch = $_SESSION['branch'];
		$lcref = $_SESSION['ref'];

		if(isset($_POST['cbono_si']) && isset($_POST['cbocd_brnch'])) {
			include_once 'includes/server.php';
			$search = (!empty($_POST['cbono_si']) ? mysqli_real_escape_string($conn,$_POST['cbono_si']) : '');
			$cd_brnch = (!empty($_POST['cbocd_brnch']) ? mysqli_real_escape_string($conn,base64_decode($_POST['cbocd_brnch'])) : '');
			if(!empty($search) && strlen($search) == 21) {
				$no_si = substr($search,0,10);
				$cd_item = substr($search,10,11);

				$updateTemp = "UPDATE t_tempsr SET cd_cust=NULL, cd_brnch=NULL WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
				mysqli_query($conn, $updateTemp);
				$deleteTempDet = "DELETE FROM t_tempsrd WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
				mysqli_query($conn, $deleteTempDet);

				$siSql = "SELECT a.* FROM t_si a WHERE a.cd_brnch='$cd_brnch' AND a.no_tran='$no_si'";
				$siResult = mysqli_query($conn, $siSql);
				$siResultCheck = mysqli_num_rows($siResult);
				if($siResultCheck > 0) {
					$siRow = mysqli_fetch_assoc($siResult);
					$lccd_cust = $siRow['cd_cust'];
					$lcnm_last = $siRow['nm_last'];
					$lcnm_first = $siRow['nm_first'];

					$siDetSql = "SELECT a.* FROM t_sid a WHERE a.no_tran='$no_si' AND a.cd_item='$cd_item'";
					$siDetResult = mysqli_query($conn, $siDetSql);
					$siDetResultCheck = mysqli_num_rows($siDetResult);
					if($siDetResultCheck > 0) {
						$siDetRow = mysqli_fetch_assoc($siDetResult);
						$lccd_stock = $siDetRow['cd_stock'];
						$lnqty = $siDetRow['qty'] * -1;
						$lnuprc = $siDetRow['uprc'];
						$lnamount = ($lnqty * $lnuprc);
						$lndisc = $siDetRow['disc'] * -1;
						$lntotal = ($lnamount - $lndisc);

						$stockSql = "SELECT qty_si, qty_sr FROM r_stock WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$cd_item'";
						$stockResult = mysqli_query($conn, $stockSql);
						$stockResultCheck = mysqli_num_rows($stockResult);
						if($stockResultCheck > 0) {
							$stockRow = mysqli_fetch_assoc($stockResult);
							$lnqty_si = $stockRow['qty_si'];
							$lnqty_sr = $stockRow['qty_sr'];
							if(($lnqty_si - $lnqty_sr) > 0) {
								$insertTempDet = "INSERT INTO t_tempsrd(ty_tran,no_tran,cd_item, cd_stock, qty, uprc, amount, disc, cd_user) VALUES('SR','$lcref','$cd_item','$lccd_stock','$lnqty','$lnuprc','$lnamount','$lndisc','$lccd_user')";
								mysqli_query($conn, $insertTempDet);
							} else {
								switch(substr($cd_item, 0, 2)) {
									case 'KL':
										echo "This item has already been returned by the customer.";
										break;
									case 'AC':
										echo "All sold items for this Accessory have already been returned.";
										break;
									default:
										break;
								}
							}
							$updateTemp = "UPDATE t_tempsr SET cd_brnch='$cd_brnch', no_si='$no_si',cd_cust='$lccd_cust' WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
							mysqli_query($conn, $updateTemp);
						} else {
							echo "Unknown error.";
						}
					} else {
						echo "Not found.";
					}
				} else {
					echo "Not found.";
				}
			} else {
				echo "Invalid search Parameters.";
			}
			mysqli_close($conn);
		} else {
			echo "Invalid Url Parameters.";
		}
	} else {
		echo "Session Expired. Please login again.";
	}
?>