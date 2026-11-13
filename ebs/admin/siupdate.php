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
	if(isset($_SESSION['id']) && isset($_SESSION['ref'])) {
		$lccd_user = $_SESSION['id'];
		$lccd_group = $_SESSION['group'];
		$lccd_brnch = $_SESSION['branch'];
		$lcref = $_SESSION['ref'];

		if(isset($_POST['txtcust_last']) && isset($_POST['txtcust_first']) && isset($_POST['txtcust_contact1']) && isset($_POST['cboty_pay1']) && isset($_POST['txtamt_pay1']) && isset($_POST['txtref_pay1']) && isset($_POST['cboty_pay2']) && isset($_POST['txtamt_pay2']) && isset($_POST['txtref_pay2']) && isset($_POST['cbocd_prtnr']) && isset($_POST['txtref_no']) && isset($_POST['cbostatus']) && isset($_POST['txtauthPIN'])) {
			include_once 'includes/server.php';

			$nm_last = strtoupper(mysqli_real_escape_string($conn, $_POST['txtcust_last']));
			$nm_first = strtoupper(mysqli_real_escape_string($conn, $_POST['txtcust_first']));
			$no_contact1 = $_POST['txtcust_contact1'];
			$ty_pay1 = (!empty($_POST['cboty_pay1']) ? base64_decode(mysqli_real_escape_string($conn, $_POST['cboty_pay1'])) : '');
	        $amt_pay1 = (empty($_POST['txtamt_pay1']) ? 0.00 : ($_POST['txtamt_pay1'] < 0 ? 0.00 : $_POST['txtamt_pay1']));
	        $ref_pay1 = strtoupper(mysqli_real_escape_string($conn, $_POST['txtref_pay1']));
			$ty_pay2 = (!empty($_POST['cboty_pay2']) ? base64_decode(mysqli_real_escape_string($conn, $_POST['cboty_pay2'])) : '');
	        $amt_pay2 = (empty($_POST['txtamt_pay2']) ? 0.00 : ($_POST['txtamt_pay2'] < 0 ? 0.00 : $_POST['txtamt_pay2']));
	        $ref_pay2 = strtoupper(mysqli_real_escape_string($conn, $_POST['txtref_pay2']));
			$tot_pay = ($amt_pay1 + $amt_pay2);
			$cd_prtnr = (!empty($_POST['cbocd_prtnr']) ? base64_decode(mysqli_real_escape_string($conn, $_POST['cbocd_prtnr'])) : '');
			$ref_no = strtoupper(mysqli_real_escape_string($conn, $_POST['txtref_no']));
			$status = strtoupper(mysqli_real_escape_string($conn, $_POST['cbostatus']));
			$authPIN = mysqli_real_escape_string($conn, $_POST['txtauthPIN']);

			date_default_timezone_set('Asia/Manila');
	        $lddt_stamp = date('Y-m-d');
			$ltt_stamp = date('Y-m-d H:i:s');
			if(!empty($authPIN)) {
				$tranSql = "SELECT a.cd_brnch, a.ty_pay1, a.amt_pay1, a.ty_pay2, a.amt_pay2, a.tot_net, a.no_otp, a.t_add FROM t_si a WHERE a.no_tran='$lcref'";
				$tranResult = mysqli_query($conn, $tranSql);
				$tranResultCheck = mysqli_num_rows($tranResult);
				if($tranResultCheck > 0) {
					$tranRow = mysqli_fetch_assoc($tranResult);
					$lccd_brnch = $tranRow['cd_brnch'];
					$lcty_pay1 = $tranRow['ty_pay1'];
					$lnamt_pay1 = $tranRow['amt_pay1'];
					$lcty_pay2 = $tranRow['ty_pay2'];
					$lnamt_pay2 = $tranRow['amt_pay2'];
					$lntot_pay = ($lnamt_pay1 + $lnamt_pay2);
					$lntot_net = ($tranRow['tot_net']);
					$lcno_otp = $tranRow['no_otp'];
					$ltt_add = $tranRow['t_add'];

					if($lcno_otp == $authPIN) {
						//Retrieve Inventory...
						$tranDetSql = "SELECT * FROM t_sid WHERE no_tran = '$lcref'";
						$tranDetResult = mysqli_query($conn, $tranDetSql);
						$tranDetResultCheck = mysqli_num_rows($tranDetResult);
						if($tranDetResultCheck > 0) {
							while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
								$lccd_stock = $tranDetRow['cd_stock'];
								$lccd_item = $tranDetRow['cd_item'];
								$lnqty = $tranDetRow['qty'];

								$deleteTranDet = "DELETE FROM t_sid WHERE no_tran='$lcref' AND cd_stock='$lccd_stock' AND cd_item='$lccd_item'";
								mysqli_query($conn, $deleteTranDet);

								$updateStock = "UPDATE r_stock SET qty_si=qty_si - '$lnqty' WHERE cd_brnch='$lccd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
								mysqli_query($conn, $updateStock);
							}
						}

						//Revert Cash register...
						if(!empty($lcty_pay1) && !empty($lcty_pay2)) {
							$updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales - '$lnamt_pay1', amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit - '$lnamt_pay1' END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay1' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
							mysqli_query($conn,$updateCreg1);

							$updateCreg2 = "UPDATE t_creg SET amt_sales=amt_sales - '$lnamt_pay2', amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit - '$lnamt_pay2' END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay2' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
							mysqli_query($conn,$updateCreg2);
						} else {
							$updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales - ".($lntot_pay >= $lntot_net ? "'$lntot_net'":"'$lnamt_pay1'").", amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit - ".($lntot_pay >= $lntot_net ? "'$lntot_net'":"'$lnamt_pay1'")." END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$lcty_pay1' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
							mysqli_query($conn,$updateCreg1);
						}

                        if($status == 'A') {
                        	$tempDetSql = "SELECT a.* FROM t_tempsid a WHERE a.no_tran='$lcref' AND a.cd_user='$lccd_user'";
							$tempDetResult = mysqli_query($conn, $tempDetSql);
							$tempDetResultCheck = mysqli_num_rows($tempDetResult);
							if($tempDetResultCheck > 0) {
								$lnsqc_no = 0;
								$lntot_gross= $lntot_disc= $lntot_net= 0.00;
								while($tempDetRow = mysqli_fetch_assoc($tempDetResult)) {
									$lnsqc_no++;
									$lccd_stock = $tempDetRow['cd_stock'];
									$lccd_item = $tempDetRow['cd_item'];
									$lnqty = $tempDetRow['qty'];
									$lnuprc = $tempDetRow['uprc'];
									$lnamount = ($lnqty * $lnuprc);
									$lndisc = $tempDetRow['disc'];
									$lntotal = ($lnamount - $lndisc);

									if(substr($lccd_item,0,2)!='AD') {
										$lntot_qty = 0.00;
										while($lntot_qty < $lnqty) {
				                            $stocklst = "SELECT a.*, (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si AS qty_soh FROM r_stock a WHERE a.cd_brnch='$lccd_brnch' AND a.cd_item='$lccd_item' AND (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si > 0 ORDER BY a.t_add ASC";
				                            $stockResult = mysqli_query($conn, $stocklst);
				                            $stockResultCheck = mysqli_num_rows($stockResult);
				                            if($stockResultCheck > 0) {
				                                $stockRow = mysqli_fetch_assoc($stockResult);
				                                $lnqty_soh = $stockRow['qty_soh'];
				                                $lccd_stock = $stockRow['no_tran'];

				                                if($lnqty_soh>=($lnqty-$lntot_qty)) {
				                                    $lnqty_si=($lnqty-$lntot_qty);
				                                } else {
				                                    $lnqty_si=$lnqty_soh;
				                                }
				                                $lnamount = $lnqty_si * $lnuprc;
				                                $lntotal = $lnamount;
				                                $lntot_gross += $lnamount;
				                                $lntot_qty += $lnqty_si;

				                                $insertTranDet = "INSERT INTO t_sid(no_tran,sqc_no,cd_item,cd_stock,qty,uprc,amount,total) VALUES('$lcref','$lnsqc_no','$lccd_item','$lccd_stock','$lnqty_si','$lnuprc','$lnamount','$lntotal')";
												mysqli_query($conn,$insertTranDet);

				                                $updateStock = "UPDATE r_stock SET qty_si=qty_si+ '$lnqty_si', t_mod='$ltt_stamp' WHERE cd_brnch='$lccd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
				                                mysqli_query($conn,$updateStock);
				                            }
				                        }
				                    } else {
				                    	$lntot_gross += $lnamount;
										$sql = "INSERT INTO t_sid(no_tran,sqc_no,cd_item,cd_stock,qty,uprc,amount,total) VALUES('$lcref','$lnsqc_no','$lccd_item','$lccd_stock','$lnqty','$lnuprc','$lnamount','$lntotal')";
										mysqli_query($conn,$sql);
				                    }
			                        $lntotal = $lntotal - $lndisc;
			                        $lntot_disc += $lndisc;

			                        $updateTranDet = "UPDATE t_sid SET disc='$lndisc', total=total-'$lndisc' WHERE cd_stock='$lccd_stock' AND cd_item='$lccd_item'";
			                        mysqli_query($conn, $updateTranDet);
								}
								$lntot_net = $lntot_gross - $lntot_disc;
								$amt_chng = (($tot_pay - $lntot_net) <=0 ? 0.00 : ($tot_pay - $lntot_net));
								$amt_bal = (($lntot_net - $tot_pay) <=0 ? 0.00 : ($lntot_net - $tot_pay));

								//Update Customer table
								$cd_cust = '';
								if(!empty($nm_last) && !empty($nm_first)) {
									$custSql = "SELECT cd_cust FROM r_cust WHERE nm_last='$nm_last' AND nm_first='$nm_first'";
									$custResult = mysqli_query($conn, $custSql);
									$custResultCheck = mysqli_num_rows($custResult);
									if($custResultCheck > 0) {
										$custRow = mysqli_fetch_assoc($custResult);
										$cd_cust = $custRow['cd_cust'];

										if(!empty($no_contact1)) {
											$updateCust = "UPDATE r_cust SET no_contact1='$no_contact1' WHERE cd_cust='$cd_cust'";
											mysqli_query($conn, $updateCust);
										}
									} else {
										$prefix = 'CU';
										$cd_cust = docno($prefix,$conn);

										$insertCust = "INSERT INTO r_cust (cd_cust,nm_last, nm_first, no_contact1, user_add, t_add, status) VALUES('$cd_cust','$nm_last','$nm_first','$no_contact1','$lccd_user','$ltt_stamp','A')";
										mysqli_query($conn, $insertCust);
									}
								}

								$updateTran = "UPDATE t_si SET total='$lntot_gross', tot_disc='$lntot_disc', tot_net='$lntot_net', ty_pay1='$ty_pay1', ty_pay2='$ty_pay2', amt_pay1='$amt_pay1', ref_pay1='$ref_pay1', amt_pay2='$amt_pay2', ref_pay2='$ref_pay2', tot_pay='$tot_pay', amt_chng='$amt_chng', amt_bal='$amt_bal', cd_cust='$cd_cust', nm_last='$nm_last', nm_first='$nm_first', no_contact1='$no_contact1', user_mod='$lccd_user', t_mod='$ltt_stamp', status='$status' WHERE no_tran='$lcref'";
								mysqli_query($conn, $updateTran);

	                            //Update Cash Register
								if(!empty($ty_pay1) && !empty($ty_pay2)) {
									$updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales+'$amt_pay1', amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit + '$amt_pay1' END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay1' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
									mysqli_query($conn,$updateCreg1);

									$updateCreg2 = "UPDATE t_creg SET amt_sales=amt_sales+'$amt_pay2', amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit + '$amt_pay2' END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay2' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
									mysqli_query($conn,$updateCreg2);
								} else {
									$updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales + ".($tot_pay >= $lntot_net ? "'$lntot_net'":"'$amt_pay1'").", amt_remit=CASE WHEN status='A' THEN 0 ELSE amt_remit + ".($tot_pay >= $lntot_net ? "'$lntot_net'":"'$amt_pay1'")." END WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay1' AND ('$ltt_add' BETWEEN t_add AND COALESCE(t_mod, '$ltt_add'))";
									mysqli_query($conn,$updateCreg1);
								}

								$amt_bal = $lntot_net - $tot_pay;
								$updateFinance= "UPDATE t_finance SET cd_prtnr='$cd_prtnr', ref_no='$ref_no', cd_cust='$cd_cust', no_si='$lcref', tot_net='$lntot_net', ty_pay1='$ty_pay1', ty_pay2='$ty_pay2', amt_dp1='$amt_pay1', amt_dp2='$amt_pay2', amt_loan='$amt_bal', user_mod='$lccd_user', t_mod='$ltt_stamp' WHERE no_si='$lcref'";
								mysqli_query($conn, $updateFinance);
							}
                        } elseif($status == 'V') {
                        	$updateTran = "UPDATE t_si SET total='', tot_disc='', tot_net='', ty_pay1='', amt_pay1='', ref_pay1='', ty_pay2='', amt_pay2='', ref_pay2='', tot_pay='', amt_bal='', amt_chng='', cd_cust='', no_otp='', status='$status' WHERE no_tran='$lcref'";
                        	mysqli_query($conn, $updateTran);


							$updateFinance= "UPDATE t_finance SET ref_no='', tot_net='', ty_pay1='', ty_pay2='', ref_pay1='', ref_pay2='', amt_dp1='', amt_dp2='', amt_loan='', amt_remit='', user_mod='$lccd_user', t_mod='$ltt_stamp', status='$status' WHERE no_si='$lcref'";
							mysqli_query($conn, $updateFinance);
                        }
						

						$deleteTemp = "DELETE FROM t_tempsi WHERE cd_user='$lccd_user'";
						mysqli_query($conn, $deleteTemp);
						$deleteTempDet = "DELETE FROM t_tempsid WHERE cd_user='$lccd_user'";
						mysqli_query($conn, $deleteTempDet);

						unset($_SESSION['ref']);
					} else {
						echo "Invalid Authorization Code.";
					}
				} else {
					echo "Reference ID could not be found.";
				}
			} else {
				echo "Invalid Authorization Code.";
			}
			mysqli_close($conn);
		} else {
			echo "Invalid Url Parameters.";
		}
	} else {
		echo "Session Expired. Please login again.";
	}
?>