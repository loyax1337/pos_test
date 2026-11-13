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
if(isset($_SESSION['id'])) {
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    $lccd_user = $_SESSION['id'];
    $lcref = $_SESSION['ref'];

    if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch'])) {
        include_once 'includes/server.php';
        $lccd_menu = '018';

        $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $llaction = $row['edit'];
            if($llaction=='1') {
                $dt_tran = $_POST['txtdt_tran'];
                $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
                $cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');
                //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

                date_default_timezone_set('Asia/Manila');
                $ltt_stamp = date('Y-m-d H:i:s');

                $tranSql = "SELECT no_tran, cd_brnch FROM t_pr WHERE no_tran = '$lcref'";
                $tranResult = mysqli_query($conn, $tranSql);
                $tranResultCheck = mysqli_num_rows($tranResult);
                if($tranResultCheck > 0) {
                    $tranRow = mysqli_fetch_assoc($tranResult);
                    $lccd_brnch = $tranRow['cd_brnch'];

                    if(empty($dt_tran) || empty($cd_brnch)) {
                        echo 'Please input required fields completely.';
                    } else {
                        $llcheck = true;
                        //Check Inventory...
                        $tempDetSql = "SELECT a.*, b.nm_item FROM t_tempprd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran='$lcref'";
                        $tempDetResult = mysqli_query($conn, $tempDetSql);
                        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                        if($tempDetResultCheck > 0) {
                            while($tempDetRow = mysqli_fetch_assoc($tempDetResult)) {
                                $lccd_item = $tempDetRow['cd_item'];
                                $lccd_stock = $tempDetRow['cd_stock'];
                                $lcnm_item = $tempDetRow['nm_item'];
                                $lnqty = $tempDetRow['qty'];

                                $lnqty_pr = 0;
                                $tranDetSql = "SELECT qty FROM t_prd WHERE no_tran='$lcref' AND cd_item='$lccd_item' AND cd_stock='$lccd_stock'";
                                $tranDetResult = mysqli_query($conn, $tranDetSql);
                                $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                                if($tranDetResultCheck > 0) {
                                    $tranDetRow = mysqli_fetch_assoc($tranDetResult);
                                    $lnqty_pr = $tranDetRow['qty'];
                                }

                                $stockSql = "SELECT SUM((qty+qty_rr+qty_pr+qty_stin+qty_stot+qty_ajin+qty_ajot+qty_sr) - qty_si) AS qty_soh FROM r_stock WHERE cd_brnch='$lccd_brnch' AND cd_item='$lccd_item'";
                                $stockResult = mysqli_query($conn, $stockSql);
                                $stockResultCheck = mysqli_num_rows($stockResult);
                                if($stockResultCheck > 0) {
                                    $stockRow = mysqli_fetch_assoc($stockResult);
                                    $lnqty_soh = $stockRow['qty_soh'];

                                    if($lnqty > ($lnqty_soh + $lnqty_pr)) {
                                        $llcheck = false;
                                        echo "Insufficient inventory of ".$lcnm_item.". "."Remaining Qty: ".number_format(($lnqty_soh + $lnqty_pr))."<br>";
                                    }
                                } else {
                                    $llcheck = false;
                                    echo "Please set beginning balance for Item: ".$lcnm_item."<br>";
                                }
                            }
                        }
                        if($llcheck) {
                            $tranDetSql = "SELECT cd_stock, cd_item, qty, ucst FROM t_prd WHERE no_tran='$lcref'";
                            $tranDetResult = mysqli_query($conn,$tranDetSql);
                            $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                            if($tranDetResultCheck > 0) {
                                while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
                                    $lccd_stock = $tranDetRow['cd_stock'];
                                    $lccd_item = $tranDetRow['cd_item'];
                                    $lnqty = $tranDetRow['qty'];

                                    $deleteTranDet = "DELETE FROM t_prd WHERE no_tran='$lcref' AND cd_item='$lccd_item'";
                                    mysqli_query($conn, $deleteTranDet);

                                    $updateStockRef = "UPDATE r_stock SET qty_pr=qty_pr+'$lnqty' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                    mysqli_query($conn,$updateStockRef);
                                }
                            }

                            $lntot_cst = 0.00;
                            $tempDetSql = "SELECT * FROM t_tempprd WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
                            $tempDetResult = mysqli_query($conn,$tempDetSql);
                            $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                            if($tempDetResultCheck > 0) {
                                while ($tempDetRow = mysqli_fetch_assoc($tempDetResult)) {
                                    $lccd_item = $tempDetRow['cd_item'];
                                    $lnqty = $tempDetRow['qty'];
                                    $lnucst = $tempDetRow['ucst'];
                                    $lnamt_cst = ($lnqty * $lnucst);

                                    $lntot_qty = 0;
                                    while($lntot_qty < $lnqty) {
                                        $stocklst = "SELECT a.*, (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si AS qty_soh FROM r_stock a WHERE a.cd_brnch='$cd_brnch' AND a.cd_item='$lccd_item' AND (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si > 0 ORDER BY a.t_add ASC";
                                        $stockResult = mysqli_query($conn, $stocklst);
                                        $stockResultCheck = mysqli_num_rows($stockResult);
                                        if($stockResultCheck > 0) {
                                            $stockRow = mysqli_fetch_assoc($stockResult);
                                            $lnqty_soh = $stockRow['qty_soh'];
                                            $lccd_stock = $stockRow['no_tran'];
                                            $lnucst = $stockRow['ucst'];

                                            if($lnqty_soh>=($lnqty-$lntot_qty)) {
                                                $lnqty_pr=($lnqty-$lntot_qty);
                                            } else {
                                                $lnqty_pr=$lnqty_soh;
                                            }
                                            $lnamt_cst = $lnqty_pr * $lnucst;
                                            $lntot_cst += $lnamt_cst;
                                            $lntot_qty += $lnqty_pr;

                                            $insertTranDet = "INSERT INTO t_prd(no_tran,cd_stock,cd_item,qty,ucst,amt_cst) VALUES('$lcref','$lccd_stock','$lccd_item','$lnqty','$lnucst','$lnamt_cst')";
                                            mysqli_query($conn,$insertTranDet);

                                            $updateStock = "UPDATE r_stock SET qty_pr=qty_pr - '$lnqty_pr' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                            mysqli_query($conn, $updateStock);
                                        }
                                    }
                                }
                            }
                            $updateTran = "UPDATE t_pr SET dt_tran='$dt_tran', cd_brnch='$cd_brnch', ref_no='$ref_no', tot_cst='$lntot_cst', user_mod='$lccd_user', t_mod='$ltt_stamp' WHERE no_tran='$lcref'";
                            mysqli_query($conn,$updateTran);

                            $deleteTemp="DELETE FROM t_temppr WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                            mysqli_query($conn, $deleteTemp);
                            $deleteTempDet="DELETE FROM t_tempprd WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                            mysqli_query($conn, $deleteTempDet);
                        }
                        $lcref ='';
                    }
                } else {
                    echo "Reference ID could not be found.";
                }
            } else {
                echo 'Access denied! Group access rights cannot perform this action.';
            }
        } else {
            echo 'Access denied! Group access rights cannot perform this action.';
        }
        mysqli_close($conn);
    } else {
        echo 'Invalid Url Parameters.';
    }
} else {
    echo "Session Expired. Please log in again.";
}
?>