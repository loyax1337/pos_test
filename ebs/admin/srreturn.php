<?php
session_start();

if(isset($_SESSION['id'])) {
	$lccd_user = $_SESSION['id'];
	$lcref = $_SESSION['ref'];

	if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch'])) {
		include_once 'includes/server.php';

		$dt_tran = $_POST['txtdt_tran'];
        $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
        $cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');

		date_default_timezone_set('Asia/Manila');
        $ltt_stamp = date('Y-m-d H:i:s');


        $tempSql = "SELECT * FROM t_tempsr WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
        $tempResult = mysqli_query($conn, $tempSql);
        $tempResultCheck = mysqli_num_rows($tempResult);
        if($tempResultCheck > 0) {
        	$tempRow = mysqli_fetch_assoc($tempResult);
        	$lcstatus = $tempRow['status'];

        	if($lcstatus == 'F') {
                echo "Already returned to the Customer.";
            } else {
                if($lcstatus == 'U') {
                    echo "Already upgraded the unit of Customer.";
                } else {
                    if($lcstatus == 'C') {
                        echo "Replacement for this unit has already been made.";
                    } else {
                    	if($lcstatus == 'R') {
	                        echo "Refund for this unit has already been made.";
	                    } else {
	                    	$tempDetSql = "SELECT * FROM t_tempsrd WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
				        	$tempDetResult = mysqli_query($conn, $tempDetSql);
				        	$tempDetResultCheck = mysqli_num_rows($tempDetResult);
				        	if($tempDetResultCheck > 0) {
				        		$llcheck = true;
				        		//Validating and checking inventory...
				        		while($tempDetRow = mysqli_fetch_assoc($tempDetResult)) {
				        			$lcty_tran = $tempDetRow['ty_tran'];
				        			$lccd_stock = $tempDetRow['cd_stock'];
				        			$lccd_item = $tempDetRow['cd_item'];
				        			$lnqty = $tempDetRow['qty'];

				        			if(!$llcheck) {
				        				continue;
				        			}

				        			if($lcty_tran == 'SR') {
				        				if(substr($lccd_item,0,2) == 'KL') {
				        					$stockSql = "SELECT SUM((qty+qty_rr+qty_pr+qty_stin+qty_stot+qty_ajin+qty_ajot+qty_sr) - qty_si) AS qty_soh FROM r_stock WHERE cd_brnch='$cd_brnch' AND cd_item='$lccd_item'";
						        			$stockResult = mysqli_query($conn, $stockSql);
						        			$stockResultCheck = mysqli_num_rows($stockResult);
						        			if($stockResultCheck > 0) {
						        				$stockRow = mysqli_fetch_assoc($stockResult);
						        				$lnqty_soh = $stockRow['qty_soh'];
						        				
						        				if($lnqty_soh < abs($lnqty)) {
						        					$llcheck = false;
						        					echo "This item can't locate in your branch.";
						        				}
						        			}
				        				} else {
				        					$llcheck = false;
				        					echo "This feature is applicable for Units only.";
				        				}
				        			} else {
				        				$llcheck = false;
				        				echo "Please remove all replacement unit.";
				        			}
				        		}
				        		if($llcheck) {
				        			$status = 'F';

				        			$tempDetSql = "SELECT * FROM t_tempsrd WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
				        			$tempDetResult = mysqli_query($conn, $tempDetSql);
				        			$tempDetResultCheck = mysqli_num_rows($tempDetResult);
				        			if($tempDetResultCheck > 0) {
				        				$tempDetRow = mysqli_fetch_assoc($tempDetResult);
				        				$lcty_tran = $tempDetRow['ty_tran'];
					        			$lccd_stock = $tempDetRow['cd_stock'];
					        			$lccd_item = $tempDetRow['cd_item'];
					        			$lnqty = $tempDetRow['qty'];

					        			$stockSql = "SELECT ((qty+qty_rr+qty_pr+qty_stin+qty_stot+qty_ajin+qty_ajot+qty_sr) - qty_si) AS qty_soh FROM r_stock WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
					        			$stockResult = mysqli_query($conn, $stockSql);
					        			$stockResultCheck = mysqli_num_rows($stockResult);
					        			if($stockResultCheck > 0) {
					        				$stockRow = mysqli_fetch_assoc($stockResult);
					        				$lnqty_soh = $stockRow['qty_soh'];

					        				if($lnqty_soh > 0) {
					        					$updateStock = "UPDATE r_stock SET qty_sr=qty_sr + $lnqty WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
					        					mysqli_query($conn, $updateStock);
					        				} else {
					        					$stockSql = "SELECT no_tran FROM r_stock WHERE cd_brnch='$cd_brnch' AND no_tran!='$lccd_stock' AND cd_item='$lccd_item' AND ((qty+qty_rr+qty_pr+qty_stin+qty_stot+qty_ajin+qty_ajot+qty_sr) - qty_si) > 0 ORDER BY t_add DESC ";
							        			$stockResult = mysqli_query($conn, $stockSql);
							        			$stockResultCheck = mysqli_num_rows($stockResult);
							        			if($stockResultCheck > 0) {
							        				$stockRow = mysqli_fetch_assoc($stockResult);
							        				$lccd_stock = $stockRow['no_tran'];

							        				$updateStock = "UPDATE r_stock SET qty_si=qty_si + abs($lnqty) WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
							        				mysqli_query($conn, $updateStock); 
							        			}
					        				}
					        			}       			
				        			}

				        			$updateTran = "UPDATE t_sr SET status='$status', user_mod='$lccd_user', t_mod='$ltt_stamp' WHERE no_tran='$lcref'";
				        			mysqli_query($conn, $updateTran);
				        		}
				        	} else {
				        		echo "Temporary detail data is compromised.";
				        	}
	                    }
                    }
                }
            }
        } else {
        	echo "Temporary data is compromised";
        }
		mysqli_close($conn);
	} else {
		echo "Invalid Url Parameters.";
	}
} else {
	echo "Session Expired. Please login again.";
}


?>