<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		$lccd_brnch = $_SESSION['branch'];
		$lcref = $_SESSION['ref'];

		if(isset($_POST['edtremarks']) && isset($_POST['cboty_pay']) && isset($_POST['txtamount'])) {
			include_once 'includes/server.php';

			$remarks = trim(mysqli_real_escape_string($conn, $_POST['edtremarks']));
			$ty_pay = (empty($_POST['cboty_pay']) ? '' : mysqli_real_escape_string($conn, base64_decode($_POST['cboty_pay'])));
			$amount = (empty($_POST['txtamount']) ? 0.00 : (($_POST['txtamount'] <=0) ? 0.00 : $_POST['txtamount']));

			date_default_timezone_set('Asia/Manila');
		    $lddt_stamp = date('Y-m-d');
		    $ltt_stamp = date('Y-m-d H:i:s');
			if(!empty($remarks)) {
				if(!empty($ty_pay)) {
					if($amount > 0) {
						$expSql = "SELECT * FROM t_exp WHERE no_tran='$lcref'";
						$expResult = mysqli_query($conn, $expSql);
						$expResultCheck = mysqli_num_rows($expResult);
						if($expResultCheck > 0) {
							$expRow = mysqli_fetch_assoc($expResult);
							$lcty_pay = $expRow['ty_pay'];
							$lnamount = $expRow['amount'];

							$cregSql = "SELECT * FROM t_creg WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay' AND status='A'";
							$cregResult = mysqli_query($conn, $cregSql);
							$cregResultCheck = mysqli_num_rows($cregResult);
							if($cregResultCheck > 0) {
								$cregRow = mysqli_fetch_assoc($cregResult);
								$lnamt_bal = ((($cregRow['amt_beg'] + $cregRow['amt_sales']) - $cregRow['amt_exp']) + (($lcty_pay==$ty_pay) ? $lnamount: 0.00));

								if($lnamt_bal >= $amount) {
									//Retrieve Cash Register...
									$updateCreg = "UPDATE t_creg SET amt_exp=amt_exp - '$lnamount' WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay' AND status='A'";
									mysqli_query($conn, $updateCreg);

									$updateTran = "UPDATE t_exp SET remarks='$remarks', ty_pay='$ty_pay', amount='$amount', user_mod='$lccd_user', t_mod='$ltt_stamp' WHERE no_tran='$lcref'";
									mysqli_query($conn, $updateTran);

									//Update Cash Register...
									$updateCreg = "UPDATE t_creg SET amt_exp=amt_exp + '$amount' WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay' AND status='A'";
									mysqli_query($conn, $updateCreg);
								} else {
									echo "Insufficient funds.";
								}
							}
						} else {
							echo "Reference ID could not be found.";
						}
					} else {
						echo "Please enter a valid Amount.";
					}
				} else {
					echo "Please select a Source.";
				}
			} else {
				echo "Please fill in the remarks.";
			}
			mysqli_close($conn);
		} else {
			echo "Invalid URL Parameters.";
		}
	} else {
		echo "Session Expired. Please login again.";
	}
?>