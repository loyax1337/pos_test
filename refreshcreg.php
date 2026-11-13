<?php
	ini_set('max_execution_time', '1200'); //300 seconds = 5 minutes
	include_once 'includes/server.php';

	$updateCreg = "UPDATE t_creg SET amt_beg=0.00, amt_sales=0.00, amt_exp=0.00, amt_remit=0.00";
	mysqli_query($conn, $updateCreg);

	$siSql = "SELECT * FROM t_si";
	$siResult = mysqli_query($conn, $siSql);
	$siResultCheck = mysqli_num_rows($siResult);
	if($siResultCheck > 0) {
		while($siRow = mysqli_fetch_assoc($siResult)) {
			$lccd_brnch = $siRow['cd_brnch'];
			$lntot_net = $siRow['tot_net'];
			$lnamt_pay1 = $siRow['amt_pay1'];
			$lnamt_pay2 = $siRow['amt_pay2'];
			$lntot_pay = ($lnamt_pay1 + $lnamt_pay2);	
			$lcty_pay1 = $siRow['ty_pay1'];
			$lcty_pay2 = $siRow['ty_pay2'];
			$ltt_add = $siRow['t_add'];

			if(!empty($lcty_pay1) && !empty($lcty_pay2)) {
				$updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales + $lnamt_pay1 WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay1' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";

				mysqli_query($conn, $updateCreg1);

				$updateCreg2 = "UPDATE t_creg SET amt_sales=amt_sales + $lnamt_pay2 WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay2' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
				mysqli_query($conn, $updateCreg2);
			} else {
				$updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales + ".($lntot_pay >= $lntot_net ? "'$lntot_net'":"'$lnamt_pay1'")." WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay1' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
				mysqli_query($conn, $updateCreg1);
			}
			
		}
	}

	$expSql = "SELECT * FROM t_exp";
	$expResult = mysqli_query($conn, $expSql);
	$expResultCheck = mysqli_num_rows($expResult);
	if($expResultCheck > 0) {
		while($expRow = mysqli_fetch_assoc($expResult)) {
			$lccd_brnch = $expRow['cd_brnch'];
			$lnamount = $expRow['amount'];
			$lcty_pay = $expRow['ty_pay'];
			$ltt_add = $expRow['t_add'];

			$updateCreg = "UPDATE t_creg SET amt_exp=amt_exp + $lnamount WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
			mysqli_query($conn, $updateCreg);
		}
	}

	$updateCreg = "UPDATE t_creg SET amt_remit= (amt_beg + amt_sales) - amt_exp where status='R'";
	mysqli_query($conn, $updateCreg);

	echo "Done";
	mysqli_close($conn);
?>