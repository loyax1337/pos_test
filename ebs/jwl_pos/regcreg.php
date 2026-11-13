<?php
	session_start();

	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		$lccd_brnch = $_SESSION['branch'];
		$lccd_group = $_SESSION['group'];
		
		include_once 'includes/server.php';
		date_default_timezone_set('Asia/Manila');
	    $lddt_stamp = date('Y-m-d');
		$ltt_stamp = date('Y-m-d H:i:s');

		$status = 'A';
		
        $lntot_beg= $lntot_sales= $lntot_refund= $lntot_exp= $lntot_bal= 0.00;

        $typaySql = "SELECT ty_pay FROM r_typay WHERE status='A'";
        $typayResult = mysqli_query($conn, $typaySql);
        $typayResultCheck = mysqli_num_rows($typayResult);
        if($typayResultCheck > 0) {
        	while($typayRow = mysqli_fetch_assoc($typayResult)) {
        		$lcty_pay = $typayRow['ty_pay'];

        		$lnamt_beg= $lnamt_sales= $lnamt_refund= $lnamt_exp= $lnamt_bal= 0.00;
        		$cregSql = "SELECT a.* FROM t_cregjwl a WHERE a.cd_brnch='$lccd_brnch' AND a.ty_pay='$lcty_pay' AND a.status='A'";
		        $cregResult = mysqli_query($conn, $cregSql);
		        $cregResultCheck = mysqli_num_rows($cregResult);
		        if($cregResultCheck > 0) {
		        	$cregRow = mysqli_fetch_assoc($cregResult);
		        	$lnamt_beg = $cregRow['amt_beg'];
		        	$lnamt_sales = $cregRow['amt_sales'];
		        	$lnamt_refund = $cregRow['amt_refund'];
		        	$lnamt_exp = $cregRow['amt_exp'];
		        	$lnamt_bal = ((($lnamt_beg + $lnamt_sales) - $lnamt_refund) - $lnamt_exp);
		        	$ltt_add = $cregRow['t_add'];

		    		$updateCreg = "UPDATE t_cregjwl SET amt_remit='$lnamt_bal',cd_user2='$lccd_user',t_mod='$ltt_stamp',status='R' WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay' AND status='A'";
		    		mysqli_query($conn, $updateCreg);
		    		
		    		$insertCreg = "INSERT INTO t_cregjwl(cd_brnch, ty_pay, amt_beg, cd_user1, t_add, status) VALUES('$lccd_brnch', '$lcty_pay', '0.00', '$lccd_user', '$ltt_stamp', '$status')";
		    		mysqli_query($conn, $insertCreg);
		        } else {
		        	$insertCreg = "INSERT INTO t_cregjwl(cd_brnch, ty_pay, amt_beg, cd_user1, t_add, status) VALUES('$lccd_brnch', '$lcty_pay', '0.00', '$lccd_user', '$ltt_stamp', '$status')";
		    		mysqli_query($conn,$insertCreg);
		        }
		        $_SESSION['st_creg'] = $ltt_add;
		        $_SESSION['ed_creg'] = $ltt_stamp;
        	}
        } else {
        	echo "Payment Type table is compromised";
        }
        mysqli_close($conn);
	} else {
		echo "Session Expired. Please login again.";
	}
?>