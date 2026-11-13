<?php
    require '../phpmailer/src/Exception.php';
	require '../phpmailer/src/PHPMailer.php';
	require '../phpmailer/src/SMTP.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
   	function sendEOTP($pAddress, $pSubject, $pUserName, $pDocNumber, $pOTP) {
		
		$lcAddress = 'klceb.eulmargacho@gmail.com';
		$lcAddressCC = 'klceb.kristinevillarico@gmail.com';
		$lcSubject = 'Request for an OTP';
        $mail = new PHPMailer;
		try {
            //$mail->SMTPDebug = 2;
            $mail->IsSMTP();
            $mail->Host = 'localhost';
            $mail->SMTPAuth = false;
            $mail->SMTPSecure = false;
            $mail->Port = 25;
            $mail->From = 'dev.kandlmarketing@klmktgpos.com';
            $mail->FromName = 'K&L Marketing POS';
            $mail->AddAddress($lcAddress);
            $mail->AddCC($lcAddressCC);
            $mail->Subject = $lcSubject;
            $mail->Body = $pUserName.' is attempting to modify Sales Transaction with reference number: '.$pDocNumber.". "."Here is the OTP - ".$pOTP;
    
            return $mail->Send();
        } catch (Exception $e) {
            return$mail->ErrorInfo;
        }
	}
?>
<?php
	session_start();
	if(isset($_SESSION['id'])) {
		$lccd_user = $_SESSION['id'];
		$lcnm_user = $_SESSION['name'];
		$lccd_group = $_SESSION['group'];
		$lccd_brnch1 = $_SESSION['branch'];
		$lcref = $_SESSION['ref'];

		if(isset($_POST['txtcust_last']) && isset($_POST['txtcust_first']) && isset($_POST['txtcust_contact1']) && isset($_POST['cboty_pay1']) && isset($_POST['txtamt_pay1']) && isset($_POST['txtref_pay1']) && isset($_POST['cboty_pay2']) && isset($_POST['txtamt_pay2']) && isset($_POST['txtref_pay2']) && isset($_POST['cbocd_prtnr']) && isset($_POST['txtref_no']) && isset($_POST['cbostatus'])) {
			include_once 'includes/server.php';

			$nm_last = strtoupper(mysqli_real_escape_string($conn, $_POST['txtcust_last']));
			$nm_first = strtoupper(mysqli_real_escape_string($conn, $_POST['txtcust_first']));
			$no_contact1 = $_POST['txtcust_contact1'];
			$ty_pay1 = (!empty($_POST['cboty_pay1']) ? base64_decode(mysqli_real_escape_string($conn, $_POST['cboty_pay1'])) : '');
	        $amt_pay1 = (empty($_POST['txtamt_pay1']) ? 0.00 : ($_POST['txtamt_pay1'] < 0 ? 0.00 : $_POST['txtamt_pay1']));
	        $ty_pay2 = (!empty($_POST['cboty_pay2']) ? base64_decode(mysqli_real_escape_string($conn, $_POST['cboty_pay2'])) : '');
	        $amt_pay2 = (empty($_POST['txtamt_pay2']) ? 0.00 : ($_POST['txtamt_pay2'] < 0 ? 0.00 : $_POST['txtamt_pay2']));
	        $ref_pay1 = strtoupper(mysqli_real_escape_string($conn, $_POST['txtref_pay1']));
            $ref_pay2 = strtoupper(mysqli_real_escape_string($conn, $_POST['txtref_pay2']));
	        $tot_pay = ($amt_pay1 + $amt_pay2);
			$cd_prtnr = (!empty($_POST['cbocd_prtnr']) ? base64_decode(mysqli_real_escape_string($conn, $_POST['cbocd_prtnr'])) : '');
			$ref_no = mysqli_real_escape_string($conn, $_POST['txtref_no']);
			$status = strtoupper(mysqli_real_escape_string($conn, $_POST['cbostatus']));

			if($ty_pay1 != $ty_pay2) {
				$tranSql = "SELECT cd_brnch, status FROM t_si WHERE no_tran='$lcref'";
				$tranResult = mysqli_query($conn, $tranSql);
				$tranResultCheck = mysqli_num_rows($tranResult);
				if($tranResultCheck > 0) {
					$tranRow = mysqli_fetch_assoc($tranResult);
					$lccd_brnch = $tranRow['cd_brnch'];
					$lcstatus = $tranRow['status'];

					if($lcstatus == 'A') {
						if($lccd_brnch1 == $lccd_brnch) {
							$tempDetSql = "SELECT a.*, b.nm_item FROM t_tempsid a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran='$lcref'";
					        $tempDetResult = mysqli_query($conn, $tempDetSql);
					        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
					        if($tempDetResultCheck > 0) {
					        	$lntot_gross= $lntot_disc= $lntot_net= 0.00;
					        	$llcheck = true;
					        	while($tempDetRow = mysqli_fetch_assoc($tempDetResult)) {
					        		$lccd_stock = $tempDetRow['cd_stock'];
					        		$lccd_item = $tempDetRow['cd_item'];
					        		$lcnm_item = $tempDetRow['nm_item'];
					        		$lnqty = $tempDetRow['qty'];
					        		$lnuprc = $tempDetRow['uprc'];
					        		$lnamount = ($lnqty * $lnuprc);
					        		$lndisc = $tempDetRow['disc'];
					        		$lntotal = ($lnamount - $lndisc);

					        		$lntot_gross +=  $lnamount;
					        		$lntot_disc +=  $lndisc;
					        		$lntot_net +=  $lntotal;

					        		if(substr($lccd_item,0,2)!='AD') {
						        		$lnqty_si = 0;
						        		$tranDetSql = "SELECT qty FROM t_sid WHERE no_tran='$lcref' AND cd_stock='$lccd_stock' AND cd_item='$lccd_item'";
						        		$tranDetResult = mysqli_query($conn, $tranDetSql);
						        		$tranDetResultCheck = mysqli_num_rows($tranDetResult);
						        		if($tranDetResultCheck > 0) {
						        			$tranDetRow = mysqli_fetch_assoc($tranDetResult);
						        			$lnqty_si = $tranDetRow['qty'];

						        			$stockSql = "SELECT SUM((qty+qty_rr+qty_pr+qty_stin+qty_stot+qty_ajin+qty_ajot+qty_sr) - qty_si) AS qty_soh FROM r_stock WHERE cd_brnch='$lccd_brnch' AND cd_item='$lccd_item'";
							        		$stockResult = mysqli_query($conn, $stockSql);
							        		$stockResultCheck = mysqli_num_rows($stockResult);
							        		if($stockResultCheck > 0) {
							        			$stockRow = mysqli_fetch_assoc($stockResult);
							        			$lnqty_soh = $stockRow['qty_soh'];

							        			if($lnqty > ($lnqty_soh + $lnqty_si)) {
							        				$llcheck = false;
							        				echo "Insufficient inventory of ".$lcnm_item.". "."Remaining Qty: ".number_format($lnqty_soh + $lnqty_si)."<br>";
							        			}
							        		} else {
							        			$llcheck = false;
							        			echo "Please set beginning balance for Item: ".$lcnm_item."<br>";
							        		}
						        		}
						        	}
					        	}
					        	if (!empty($cd_prtnr) && !empty($ref_no)) {
								    if ($tot_pay >= $lntot_net) {
								        $llcheck = false;
								        echo "Payment must not be either higher or equal to Total NET.<br>";
								    }

								    $prtnrSql = "SELECT max_len FROM r_prtnr WHERE cd_prtnr='$cd_prtnr' AND status='A'";
									$prtnrResult = mysqli_query($conn, $prtnrSql);
									$prtnrResultCheck = mysqli_num_rows($prtnrResult);
									if($prtnrResultCheck > 0) {
										$prtnrRow = mysqli_fetch_assoc($prtnrResult);
										$lnmax_len = $prtnrRow['max_len'];

										if(strlen($ref_no) != $lnmax_len) {
											$llcheck = false;
											echo "Partner Approval Code: it should be ".$lnmax_len." digits.";
										}
									} else {
										$llcheck = false;
										echo "Selected Partner could not be found.";
									}
								} elseif (empty($cd_prtnr) && empty($ref_no)) {
								    if($tot_pay < $lntot_net) {
								        $llcheck = false;
								        echo "Please enter a valid customer payment amount.<br>";
								    }
								} elseif (!empty($cd_prtnr) && empty($ref_no)) {
								    $llcheck = false;
								    echo "You might leave the Approval No. blank.<br>";
								} elseif (empty($cd_prtnr) && !empty($ref_no)) {
								    $llcheck = false;
								    echo "You might leave the Partner selection.<br>";
								}
					
								if(!empty($nm_last) && empty($nm_first) || empty($nm_last) && !empty($nm_first)) {
									$llcheck = false;
									echo "You might leave the Customer details."."<br>";
								}
								if(!empty($no_contact1)) {
									if(strlen($no_contact1) != 11) {
										$llcheck = false;
										echo "Customer Contact No.: It should be 11 digits."."<br>";
									}
								}
				        		if($amt_pay2 > 0 && empty($ty_pay2) || $amt_pay2 <= 0 && !empty($ty_pay2)) {
				        			$llcheck = false;
				        			echo "Please check your Payment Option 2, you might leave something."."<br>";
				        		}
				        		if(!empty($ty_pay1) && !empty($ty_pay2)) {
				        			if($tot_pay > $lntot_net) {
					        			$llcheck = false;
					        			echo "Payment Options exceeded."."<br>";
					        		}
					        		if($amt_pay1 <= 0 && $amt_pay2 > 0 || $amt_pay2 <= 0) {
					        			$llcheck = false;
					        			echo "Please check your Payment Option 1, you might leave something."."<br>";
					        		}
					        		if(($ty_pay1 != 'PM00000001' && empty($ref_pay1)) || ($ty_pay2 != 'PM00000001' && empty($ref_pay2))) {
					        			$llcheck = false;
					        			echo "Please enter Payment Reference No.";
					        		}
				        		} else {
				        			if((!empty($ty_pay1) && $ty_pay1 != 'PM00000001') && empty($ty_pay2)) {
				        				if($tot_pay > $lntot_net) {
						        			$llcheck = false;
						        			echo "Payment Options exceeded."."<br>";
						        		}
						        		if(empty($ref_pay1)) {
					        				$llcheck = false;
					        				echo "Please enter Payment Reference No.";
					        			}
				        			}
				        		}
					        	if($llcheck) {
					        		$lcOTP = rand(1000,9999);

					        		$updateTran = "UPDATE t_si SET no_otp='$lcOTP' WHERE no_tran='$lcref'";
					        		mysqli_query($conn, $updateTran);

					        		$lcretval = sendEOTP("", "", $lcnm_user, $lcref, $lcOTP);
									if($lcretval != '1') {
										echo $lcretval;
									}
					        	}
					        } else {
					        	echo "SI Item detais cannot be empty.";
					        }
						} else {
							echo "You are not authorize to modify this record.";
						}
					} elseif($lcstatus = 'V') {
						echo "Voided transactions can no longer be modified.";
					} else {
						echo "Unknown error.";
					}
				} else {
					echo "Reference ID could not be found.";
				}
			} else {
				echo "Payment Options should not be the same.";
			}
	        mysqli_close($conn);
		} else {
			echo "Invalid Url Parameters.";
		}
	} else {
		echo "Session Expired. Please login again.";
	}
?>