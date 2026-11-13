<?php
    function docno($prefix,$conn){
        $lcdocno = "";
        $select = "SELECT * FROM r_docno WHERE cd_docno='$prefix'";
        $selectResult = mysqli_query($conn, $select);
        if ($row = mysqli_fetch_assoc($selectResult)){
            if($row['st_series'] <= $row['ed_series']){
              $series = str_pad($row['st_series'],8,"0",STR_PAD_LEFT);
              $update = "UPDATE r_docno SET st_series=st_series+1 WHERE cd_docno='$prefix'";
              $updateResult = mysqli_query($conn, $update);
              $lcdocno = $prefix.$series;
            } else {
              $lcdocno = "NOFILE";
            }
        }
        return $lcdocno;
    }

	session_start();

	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		$lccd_brnch = $_SESSION['branch'];
		$lccd_group = $_SESSION['group'];

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
						$cregSql = "SELECT * FROM t_creg WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay' AND status='A'";
						$cregResult = mysqli_query($conn, $cregSql);
						$cregResultCheck = mysqli_num_rows($cregResult);
						if($cregResultCheck > 0) {
							$cregRow = mysqli_fetch_assoc($cregResult);
							$lnamt_bal = ($cregRow['amt_beg'] + $cregRow['amt_sales'] - $cregRow['amt_exp']);

							if($amount > $lnamt_bal) {
								echo "Insufficient funds.";
							} else {
								$status = 'A';
						        $prefix = 'EX';
						        $seekDocnoResultCheck = 1;
						        do {
						            $no_doc = docno($prefix,$conn);
						            
						            $seekDocno = "SELECT no_tran FROM t_exp WHERE no_tran='$no_doc'";
						            $seekDocnoResult = mysqli_query($conn, $seekDocno);
						            $seekDocnoResultCheck = mysqli_num_rows($seekDocnoResult);
						        } while($seekDocnoResultCheck > 0);
						        switch ($no_doc) {
						        case "":
						            echo 'File is missing.';
						            break;
						        case "NOFILE":
						            echo 'System reached the records limit. Please contact your Administrator and also send any documents that may support your concerns e.g. Photos or taken screenshots.';
						            break;
						        default:
						            $insertTran = "INSERT INTO t_exp(cd_brnch, no_tran, dt_tran, remarks, ty_pay, amount, user_add,t_add,status) VALUES('$lccd_brnch','$no_doc','$lddt_stamp','$remarks','$ty_pay','$amount','$lccd_user','$ltt_stamp','$status')";
						            mysqli_query($conn,$insertTran);


						            $updateCreg = "UPDATE t_creg SET amt_exp=amt_exp+'$amount' WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay' AND status='A'";
			        				mysqli_query($conn,$updateCreg);
						            break;
						        }
							}
						}
					} else {
						echo "Please enter a valid amount.";
					}
				} else {
					echo "Please select Source.";
				}
			} else {
				echo "Remarks cannot leave as blank.";
			}
		} else {
			echo "Invalid URL Parameters.";
		}
	} else {
		echo "Session Expired. Please login again.";
	}
?>