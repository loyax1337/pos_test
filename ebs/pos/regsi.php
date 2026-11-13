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
		$lccd_brnch = $_SESSION['branch'];
		$lccd_user = $_SESSION['id'];

		date_default_timezone_set('Asia/Manila');
        $lddt_stamp = date('Y-m-d');
		$ltt_stamp = date('Y-m-d H:i:s');
		if(isset($_POST['txtnm_last']) && isset($_POST['txtnm_first']) && isset($_POST['txtno_contact1']) && isset($_POST['cboty_pay1']) && isset($_POST['cboty_pay2']) && isset($_POST['txtamt_pay1']) && isset($_POST['txtamt_pay2']) && isset($_POST['txtref_pay1']) && isset($_POST['txtref_pay2']) && isset($_POST['cbocd_prtnr']) && isset($_POST['txtapp_no'])) {
			include_once 'includes/server.php';

			$nm_last = ltrim(strtoupper(mysqli_real_escape_string($conn, $_POST['txtnm_last'])));
			$nm_first = ltrim(strtoupper(mysqli_real_escape_string($conn, $_POST['txtnm_first'])));
            $no_contact1 = $_POST['txtno_contact1'];
            $ty_pay1 = (!empty($_POST['cboty_pay1']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cboty_pay1'])) : '');
            $ty_pay2 = (!empty($_POST['cboty_pay2']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cboty_pay2'])) : '');
            $amt_pay1 = ((empty($_POST['txtamt_pay1']) ? 0.00 : (($_POST['txtamt_pay1'] <= 0.00) ? 0.00 : $_POST['txtamt_pay1'])));
            $amt_pay2 = ((empty($_POST['txtamt_pay2']) ? 0.00 : (($_POST['txtamt_pay2'] <= 0.00) ? 0.00 : $_POST['txtamt_pay2'])));
            $ref_pay1 = strtoupper(mysqli_real_escape_string($conn, $_POST['txtref_pay1']));
            $ref_pay2 = strtoupper(mysqli_real_escape_string($conn, $_POST['txtref_pay2']));
            $cd_prtnr = (!empty($_POST['cbocd_prtnr']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_prtnr'])) : '');
            $app_no = strtoupper(mysqli_real_escape_string($conn, $_POST['txtapp_no']));

            $tot_pay = ($amt_pay1 + $amt_pay2);
            
			$cartSql = "SELECT a.*, b.nm_item, b.cd_unit, b.cd_model, b.cd_rom, b.cd_categ FROM t_cart a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.cart_selected=1";
			$cartResult = mysqli_query($conn, $cartSql);
			$cartResultCheck = mysqli_num_rows($cartResult);
			if($cartResultCheck > 0) {
				$llcheck = true;
				$lntot_net= 0.00;
				while($cartRow = mysqli_fetch_assoc($cartResult)) {
					if(!$llcheck) {
						continue;
					}
					$lccd_unit = $cartRow['cd_unit'];
					$lccd_item = $cartRow['cd_item'];
					$lccd_model = $cartRow['cd_model'];
					$lccd_rom = $cartRow['cd_rom'];
					$lccd_categ = $cartRow['cd_categ'];
					$lcnm_item = $cartRow['nm_item'];
					$lnqty = $cartRow['qty'];
					$lnuprc = $cartRow['uprc'];
					$lndisc = $cartRow['disc'];

					/*
					$uprcSql = "SELECT uprc FROM r_uprc WHERE cd_brnch='$lccd_brnch' AND cd_unit='$lccd_unit' AND dt_eff<='$lddt_stamp' ORDER BY dt_eff DESC";
					$uprcResult = mysqli_query($conn,$uprcSql);
					$uprcResultCheck = mysqli_num_rows($uprcResult);
					if($uprcResultCheck > 0) {
						$uprcRow = mysqli_fetch_assoc($uprcResult);
						$lnuprc = $uprcRow['uprc'];
					}
					*/

					$escaSql = "SELECT cd_esca, uprc FROM r_esca WHERE cd_model='$lccd_model' AND cd_rom='$lccd_rom' AND cd_categ='$lccd_categ' AND '$lddt_stamp' BETWEEN dt_start AND dt_end";
					$escaResult = mysqli_query($conn, $escaSql);
					$escaResultCheck = mysqli_num_rows($escaResult);
					if($escaResultCheck > 0) {
						$escaRow = mysqli_fetch_assoc($escaResult);
						$lccd_esca = $escaRow['cd_esca'];
						$lnuprc = $escaRow['uprc'];
					}

					$lnamount = ($lnqty * $lnuprc);
					$lntotal = $lnamount - $lndisc;
					$lntot_net += $lntotal;

					if(substr($lccd_item,0,2)!='AD') {
						$stockSql = "SELECT a.*, SUM((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) AS qty_soh FROM r_stock a WHERE a.cd_brnch='$lccd_brnch' AND a.cd_item='$lccd_item'";
		                $stockResult = mysqli_query($conn,$stockSql);
		                $stockResultCheck = mysqli_num_rows($stockResult);
		                if($stockResultCheck > 0) {
		                    $stockRow = mysqli_fetch_assoc($stockResult);
		                    $lnqty_soh = $stockRow['qty_soh'];
		                    if($lnqty > $lnqty_soh) {
		                    	$llcheck = false;
		                        echo "Insufficient inventory. ".$lcnm_item." "."Remaining Qty: ".number_format($lnqty_soh)."<br>";
		                    }
		                } else {
		                	$llcheck = false;
		                    echo "Please set beginning balance. ".$lcnm_item."<br>";
		                }
					}
				}
				if(empty($cd_prtnr) && empty($app_no)) {
					if($tot_pay < $lntot_net) {
						$llcheck = false;
						echo "Please enter a valid customer payment amount."."<br>";
					}
				}elseif(!empty($cd_prtnr)!='PRE-ORDER' && !empty($app_no)) {
					if($tot_pay >= $lntot_net) {
						$llcheck = false;
						echo "Payment must not be either higher or equal than Total NET."."<br>";
					}

					$prtnrSql = "SELECT max_len FROM r_prtnr WHERE cd_prtnr='$cd_prtnr' AND status='A'";
					$prtnrResult = mysqli_query($conn, $prtnrSql);
					$prtnrResultCheck = mysqli_num_rows($prtnrResult);
					if($prtnrResultCheck > 0) {
						$prtnrRow = mysqli_fetch_assoc($prtnrResult);
						$lnmax_len = $prtnrRow['max_len'];

						if($lnmax_len > 0) {
							if(strlen($app_no) != $lnmax_len) {
								$llcheck = false;
								echo "Partner Approval Code: it should be ".$lnmax_len." digits.";
							}
						}
					} else {
						$llcheck = false;
						echo "Selected Partner could not be found.";
					}
				} elseif(empty($cd_prtnr) && !empty($app_no)) {
					$llcheck = false;
					echo "You might leave the Partner selection."."<br>";
				}

				if(!empty($nm_last) && empty($nm_first) || empty($nm_last) && !empty($nm_first)) {
					$llcheck = false;
					echo "You might leave the Customer details."."<br>";
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
        			if((!empty($ty_pay1) && $ty_pay1 != 'PM00000001' && $ty_pay1 != 'PM00000009') && empty($ty_pay2)) {
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
					if(!empty($nm_last) && !empty($nm_first) && !empty($no_contact1)) {
						if(strlen($no_contact1) == 11) {
							if($ty_pay1 != $ty_pay2) {
								$financeSql = "SELECT ref_no FROM t_finance WHERE cd_prtnr='$cd_prtnr' AND ref_no='$app_no' AND status!='V'";
								$financeResult = mysqli_query($conn, $financeSql);
								$financeResultCheck = mysqli_num_rows($financeResult);
								if($financeResultCheck > 0 ) {
									echo "Duplicate Order ID, Approval No., or Loan Contract No. found.";
								} else {
									$status = 'A';
					                $prefix = 'SI';
					                $seekDocnoResultCheck = 1;
					                do {
					                    $no_doc = docno($prefix,$conn);
					                    
					                    $seekDocno = "SELECT no_tran FROM t_si WHERE no_tran='$no_doc'";
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
					                	$cartSql = "SELECT a.*, b.cd_unit, b.cd_model, b.cd_rom, b.cd_categ FROM t_cart a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.cart_selected=1";
										$cartResult = mysqli_query($conn, $cartSql);
										$cartResultCheck = mysqli_num_rows($cartResult);
										if($cartResultCheck > 0) {
											$lntot_gross= $lntot_disc= $lntot_net= 0.00;
											$lnsqc_no = 0;
											while($cartRow = mysqli_fetch_assoc($cartResult)) {
												if(!$llcheck) {
													continue;
												}
												$lnsqc_no++;
												$lccd_unit = $cartRow['cd_unit'];
												$lccd_item = $cartRow['cd_item'];
												$lccd_model = $cartRow['cd_model'];
												$lccd_rom = $cartRow['cd_rom'];
												$lccd_categ = $cartRow['cd_categ'];
												$lnqty = $cartRow['qty'];
												$lnuprc = $cartRow['uprc'];
												$lnamount = ($lnqty * $lnuprc);
												$lndisc = $cartRow['disc'];
												$lntotal = ($lnamount - $lndisc);

												/*
												$uprcSql = "SELECT uprc FROM r_uprc WHERE cd_brnch='$lccd_brnch' AND cd_unit='$lccd_unit' AND dt_eff<='$lddt_stamp' ORDER BY dt_eff DESC";
												$uprcResult = mysqli_query($conn,$uprcSql);
												$uprcResultCheck = mysqli_num_rows($uprcResult);
												if($uprcResultCheck > 0) {
													$uprcRow = mysqli_fetch_assoc($uprcResult);
													$lnuprc = $uprcRow['uprc'];
												}*/

												$escaSql = "SELECT cd_esca, uprc FROM r_esca WHERE cd_model='$lccd_model' AND cd_rom='$lccd_rom' AND cd_categ='$lccd_categ' AND '$lddt_stamp' BETWEEN dt_start AND dt_end";
												$escaResult = mysqli_query($conn, $escaSql);
												$escaResultCheck = mysqli_num_rows($escaResult);
												if($escaResultCheck > 0) {
													$escaRow = mysqli_fetch_assoc($escaResult);
													$lccd_esca = $escaRow['cd_esca'];
													$lnuprc = $escaRow['uprc'];
												}

												$lccd_stock ='';
												if(substr($lccd_item,0,2)!='AD') {
													$lntot_qty = 0.00;
													while($lntot_qty < $lnqty) {
							                            $stocklst = "SELECT a.*, (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si AS qty_soh FROM r_stock a WHERE a.cd_brnch='$lccd_brnch' AND a.cd_item='$lccd_item' AND (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si > 0 ORDER BY t_add DESC";
							                            $stockResult = mysqli_query($conn,$stocklst);
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

							                                $sql = "INSERT INTO t_sid(no_tran,sqc_no,cd_item,cd_stock,qty,uprc,amount,total) VALUES('$no_doc','$lnsqc_no','$lccd_item','$lccd_stock','$lnqty_si','$lnuprc','$lnamount','$lntotal')";
															mysqli_query($conn,$sql);

							                                $update = "UPDATE r_stock SET qty_si=qty_si+$lnqty_si, t_mod='$ltt_stamp' WHERE cd_brnch='$lccd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
							                                mysqli_query($conn,$update);
							                            }
							                        }
												} else {
													$lntot_gross += $lnamount;
													$sql = "INSERT INTO t_sid(no_tran,sqc_no,cd_item,cd_stock,qty,uprc,amount,total) VALUES('$no_doc','$lnsqc_no','$lccd_item','$lccd_stock','$lnqty','$lnuprc','$lnamount','$lntotal')";
													mysqli_query($conn,$sql);
												}
						                        $lntotal = $lntotal - $lndisc;
						                        $lntot_disc += $lndisc;

						                        $updateTranDet = "UPDATE t_sid SET disc='$lndisc', total=total-'$lndisc' WHERE no_tran='$no_doc' AND cd_stock='$lccd_stock' AND cd_item='$lccd_item'";
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

													$insertCust = "INSERT INTO r_cust (cd_cust,nm_last, nm_first, no_contact1, user_add, t_add, status) VALUES('$cd_cust','$nm_last','$nm_first','$no_contact1','$lccd_user','$ltt_stamp','$status')";
													mysqli_query($conn, $insertCust);
												}
											}

											$insertTran = "INSERT INTO t_si(cd_brnch,no_tran,dt_tran,total,tot_disc,tot_net,ty_pay1,ty_pay2,amt_pay1,amt_pay2,ref_pay1,ref_pay2,tot_pay,amt_chng,amt_bal,cd_cust,nm_last,nm_first,no_contact1,user_add,t_add,status) VALUES('$lccd_brnch','$no_doc','$lddt_stamp','$lntot_gross','$lntot_disc','$lntot_net','$ty_pay1','$ty_pay2','$amt_pay1','$amt_pay2','$ref_pay1','$ref_pay2','$tot_pay','$amt_chng','$amt_bal','$cd_cust','$nm_last','$nm_first','$no_contact1','$lccd_user','$ltt_stamp','$status')";
											mysqli_query($conn, $insertTran);

											//Update Cash Register
											if(!empty($ty_pay1) && !empty($ty_pay2)) {
												$updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales+'$amt_pay1' WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay1' AND status='A'";
												mysqli_query($conn,$updateCreg1);

												$updateCreg2 = "UPDATE t_creg SET amt_sales=amt_sales+'$amt_pay2' WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay2' AND status='A'";
												mysqli_query($conn,$updateCreg2);
											} else {
												$updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales+".($tot_pay >= $lntot_net ? "'$lntot_net'":"'$amt_pay1'")." WHERE cd_brnch='$lccd_brnch' AND ty_pay='$ty_pay1' AND status='A'";
												mysqli_query($conn,$updateCreg1);
											}

											if(!empty($cd_prtnr) && !empty($app_no)) {
												$amt_bal = $lntot_net - $tot_pay;
												$prefix = 'FN';
												$no_finance = docno($prefix,$conn);
												$insertFinance = "INSERT INTO t_finance (cd_brnch,no_tran, dt_tran, cd_prtnr, ref_no, cd_cust, no_si, tot_net, ty_pay1, ty_pay2, amt_dp1, amt_dp2, ref_pay1, ref_pay2, amt_loan, user_add, t_add, status) VALUES('$lccd_brnch','$no_finance','$lddt_stamp','$cd_prtnr','$app_no','$cd_cust','$no_doc','$lntot_net','$ty_pay1','$ty_pay2','$amt_pay1','$amt_pay2', '$ref_pay1', '$ref_pay2','$amt_bal','$lccd_user','$ltt_stamp','P')";
												mysqli_query($conn, $insertFinance);
											}

											$deleteCart = "DELETE FROM t_cart WHERE cd_user='$lccd_user'";
											mysqli_query($conn, $deleteCart);

											$_SESSION['no_si'] = $no_doc;
										}
					                	break;
					            	}
								}
				            } else {
				            	echo "Payment Options should not be the same.";
				            }
						} else {
							echo "Customer Contact No.: It should be 11 digits."."<br>";
						}
					} else {
						echo "Please enter Customer details.";
					}
				}
			} else {
				echo "Cart must not be empty.";
			}
			mysqli_close($conn);
		} else {
			echo "Invalid URL Parameters.";
		}
	} else {
		echo "Session Expired. Please login again.";
	}
?>