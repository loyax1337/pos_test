<?php

session_start();
	//opening
    $sgp = $_SESSION['group'];
    if($sgp = 'GS00000001' || $sgp = 'GS00000002' || $sgp = 'GS00000003' || $sgp = '1' || $sgp = '2' || $sgp = '3'){
    $inactivity_time = 526000 * 60;
    }else{
    // Set the inactivity time of 15 minutes (900 seconds)
    $inactivity_time = 30 * 60;
    }

    // Check if the last_timestamp is set
    // and last_timestamp is greater then 15 minutes or 9000 seconds
    // then unset $_SESSION variable & destroy session data
    if (isset($_SESSION['last_timestamp']) && (time() - $_SESSION['last_timestamp']) > $inactivity_time) {
        session_unset();
        session_destroy();

        //Redirect user to login page
        header("Location: login.php");
        exit();
    }else{
        // Regenerate new session id and delete old one to prevent session fixation attack
        session_regenerate_id(true);

        // Update the last timestamp
        $_SESSION['last_timestamp'] = time();
    }//closing bracket

if (isset($_SESSION['id'])) {
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    $lcref = $_SESSION['ref'];

    if (isset($_POST['cbocd_prtnr']) && isset($_POST['txtref_no']) && isset($_POST['cboty_pay1']) && isset($_POST['txtamt_dp1']) && isset($_POST['cboty_pay2']) && isset($_POST['txtamt_dp2']) && isset($_POST['txtamt_remit']) && isset($_POST['cbostatus'])) {
        include_once 'includes/server.php';
        $lccd_menu = '027';

        $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $llaction = $row['edit'];
            if($llaction=='1') {
            	$cd_prtnr = (!empty($_POST['cbocd_prtnr']) ? base64_decode(mysqli_real_escape_string($conn, $_POST['cbocd_prtnr'])) : '');
		        $ref_no = strtoupper(mysqli_real_escape_string($conn, $_POST['txtref_no']));
		        $ty_pay1 = (!empty($_POST['cboty_pay1']) ? base64_decode(mysqli_real_escape_string($conn, $_POST['cboty_pay1'])) : '');
		        $amt_dp1 = (empty($_POST['txtamt_dp1']) ? 0.00 : ($_POST['txtamt_dp1'] < 0 ? 0.00 : $_POST['txtamt_dp1']));
		        $ty_pay2 = (!empty($_POST['cboty_pay2']) ? base64_decode(mysqli_real_escape_string($conn, $_POST['cboty_pay2'])) : '');
		        $amt_dp2 = (empty($_POST['txtamt_dp2']) ? 0.00 : ($_POST['txtamt_dp2'] < 0 ? 0.00 : $_POST['txtamt_dp2']));
		        $tot_dp = ($amt_dp1 + $amt_dp2);
		        $amt_remit = (empty($_POST['txtamt_remit']) ? 0.00 : ($_POST['txtamt_remit'] < 0 ? 0.00 : $_POST['txtamt_remit']));
		        $status = strtoupper(mysqli_real_escape_string($conn, $_POST['cbostatus']));

		        date_default_timezone_set('Asia/Manila');
		        $ltt_stamp = date('Y-m-d H:i:s');
		        if(!empty($cd_prtnr)) {
		        	if(!empty($ref_no)) {
		        		$prtnrSql = "SELECT max_len FROM r_prtnr WHERE cd_prtnr='$cd_prtnr' AND status='A'";
						$prtnrResult = mysqli_query($conn, $prtnrSql);
						$prtnrResultCheck = mysqli_num_rows($prtnrResult);
						if($prtnrResultCheck > 0) {
							$prtnrRow = mysqli_fetch_assoc($prtnrResult);
							$lnmax_len = $prtnrRow['max_len'];

							if(strlen($ref_no) == $lnmax_len) {
								if($tot_dp >= 0) {
					            	if($ty_pay1 != $ty_pay2) {
					            		$llcheck = true;
					            		if($amt_dp1 > 0 && empty($ty_pay1) || $amt_dp1 <= 0 && !empty($ty_pay1)) {
					            			$llcheck = false;
					            			echo "Please check your Payment Option 1, you might leave something."."<br>";
					            		}
					            		if($amt_dp2 > 0 && empty($ty_pay2) || $amt_dp2 <= 0 && !empty($ty_pay2)) {
					            			$llcheck = false;
					            			echo "Please check your Payment Option 2, you might leave something."."<br>";
					            		}
					            		if($llcheck) {
					            			if($amt_remit >= 0) {
							                    if(!empty($status)) {
							                        $tranSql = "SELECT cd_brnch, no_tran, tot_net, no_si, ty_pay1, amt_dp1, ty_pay2, amt_dp2, t_add FROM t_finance WHERE no_tran='$lcref'";
							                        $tranResult = mysqli_query($conn, $tranSql);
							                        $tranResultCheck = mysqli_num_rows($tranResult);
							                        if($tranResultCheck > 0) {
							                            $tranRow = mysqli_fetch_assoc($tranResult);
							                            $lccd_brnch = $tranRow['cd_brnch'];
							                            $lcno_si = $tranRow['no_si'];
							                            $lntot_net = $tranRow['tot_net'];
							                            $lcty_pay1 = $tranRow['ty_pay1'];
							                            $lnamt_dp1 = $tranRow['amt_dp1'];
							                            $lcty_pay2 = $tranRow['ty_pay2'];
							                            $lnamt_dp2 = $tranRow['amt_dp2'];
							                            $lntot_dp = ($lnamt_dp1 + $lnamt_dp2);
							                            $lnamt_loan = ($lntot_net - $tot_dp);
							                            $ltt_add = $tranRow['t_add'];

							                            if($tot_dp < $lntot_net) {
							                                // Check for status "R"
							                                if($status === 'R' && $amt_remit != $lnamt_loan) {
							                                    echo "You cannot select the Remitted status if the Remittance is incomplete.";
							                                } else {
							                                    if($amt_remit > $lnamt_loan) {
							                                        echo "Please enter a valid Remittance amount.";
							                                    } else {
							                                        $updateTran = "UPDATE t_finance SET cd_prtnr='$cd_prtnr', ref_no='$ref_no', ty_pay1='$ty_pay1', amt_dp1='$amt_dp1', ty_pay2='$ty_pay2', amt_dp2='$amt_dp2', amt_loan='$lnamt_loan', amt_remit='$amt_remit', status='$status', user_mod='$lccd_user', t_mod='$ltt_stamp'  WHERE no_tran='$lcref'";
							                                        mysqli_query($conn, $updateTran);

							                                        //Update SI Reference...
							                                        $updateSITran = "UPDATE t_si SET ty_pay1='$ty_pay1', amt_pay1='$amt_dp1', ty_pay2='$ty_pay2', amt_pay2='$amt_dp2', amt_bal='$lnamt_loan' WHERE no_tran='$lcno_si'";
							                                        mysqli_query($conn, $updateSITran);

							                                        //Revert Cash register...
							                                        $updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales - '$lnamt_dp1', amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit - '$lnamt_dp1' END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay1' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
							                                        mysqli_query($conn, $updateCreg1);

							                                        $updateCreg2 = "UPDATE t_creg SET amt_sales=amt_sales - '$lnamt_dp2', amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit - '$lnamt_dp2' END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay2' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
							                                        mysqli_query($conn, $updateCreg2);

							                                        //Update Cash register...
							                                        $updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales + '$amt_dp1', amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit + '$amt_dp1' END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay1' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
							                                        mysqli_query($conn, $updateCreg1);

							                                        $updateCreg2 = "UPDATE t_creg SET amt_sales=amt_sales + '$amt_dp2', amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit + '$amt_dp2' END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay2' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
							                                        mysqli_query($conn, $updateCreg2);

							                                        unset($_SESSION['ref']);
							                                    }
							                                }
							                            } else {
							                                echo "Please enter a valid Down Payment.";
							                            }
							                        } else {
							                            echo "Reference ID could not be found.";
							                        }
							                    } else {
							                        echo "Please select a Status.";
							                    }
							                } else {
							                    echo "Please enter a valid Remittance amount.";
							                }
					            		}
					            	} else {
					            		echo "Payment options should not be the same.";
					            	}
					            } else {
					                echo "Please enter a valid Down Payment.";
					            }
							} else {
								echo "Partner Approval Code: it should be ".$lnmax_len." digits.";
							}
						} else {
							echo "Selected Partner could not be found.";
						}
		        	} else {
		        		echo "Partner Approval Code must not be blank.";
		        	}
		        } else {
		            echo "Please select a Partner.";
		        }
            } else {
            	echo 'Access denied! Group access rights cannot perform this action.';
            }
        } else {
        	echo 'Access denied! Group access rights cannot perform this action.';
        }
        mysqli_close($conn);
    } else {
        echo "Invalid Url Parameters.";
    }
} else {
    echo "Session Expired. Please login again.";
}
?>