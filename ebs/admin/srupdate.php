<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    $lccd_user = $_SESSION['id'];
    $lcref = $_SESSION['ref'];

    if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch']) && isset($_POST['edtremarks'])) {
        include_once 'includes/server.php';
        $lccd_menu = '029';

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
                $remarks = ltrim(mysqli_real_escape_string($conn,$_POST['edtremarks'])); 
                //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

                date_default_timezone_set('Asia/Manila');
                $ltt_stamp = date('Y-m-d H:i:s');

                $tempSql = "SELECT no_si FROM t_tempsr WHERE no_tran='$lcref'";
                $tempResult = mysqli_query($conn, $tempSql);
                $tempResultCheck = mysqli_num_rows($tempResult);
                if($tempResultCheck > 0) {
                    $tempRow = mysqli_fetch_assoc($tempResult);
                    $no_si = $tempRow['no_si'];

                    $tranSql = "SELECT no_tran, no_si, cd_brnch, cd_cust, status FROM t_sr WHERE no_tran = '$lcref'";
                    $tranResult = mysqli_query($conn, $tranSql);
                    $tranResultCheck = mysqli_num_rows($tranResult);
                    if($tranResultCheck > 0) {
                        $tranRow = mysqli_fetch_assoc($tranResult);
                        $lcno_si = $tranRow['no_si'];
                        $lccd_brnch = $tranRow['cd_brnch'];
                        $lccd_cust = $tranRow['cd_cust'];
                        $lcstatus = $tranRow['status'];

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
                                        if(empty($dt_tran) || empty($cd_brnch)) {
                                            echo 'Please input required fields completely.';
                                        } else {
                                            $llcheck = true;
                                            //Check Inventory...
                                            $tempDetSql = "SELECT a.*, b.nm_item FROM t_tempsrd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran='$lcref'";
                                            $tempDetResult = mysqli_query($conn, $tempDetSql);
                                            $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                                            if($tempDetResultCheck > 0) {
                                                while($tempDetRow = mysqli_fetch_assoc($tempDetResult)) {
                                                    $lcty_tran = $tempDetRow['ty_tran'];
                                                    $lccd_item = $tempDetRow['cd_item'];
                                                    $lccd_stock = $tempDetRow['cd_stock'];
                                                    $lcnm_item = $tempDetRow['nm_item'];
                                                    $lnqty = $tempDetRow['qty'];
                                                    
                                                    if($lnqty > 0) {
                                                        $lnqty_sr = 0;
                                                        $tranDetSql = "SELECT qty FROM t_srd WHERE ty_tran='$lcty_tran' AND no_tran='$lcref' AND cd_item='$lccd_item' AND cd_stock='$lccd_stock'";
                                                        $tranDetResult = mysqli_query($conn, $tranDetSql);
                                                        $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                                                        if($tranDetResultCheck > 0) {
                                                            $tranDetRow = mysqli_fetch_assoc($tranDetResult);
                                                            $lnqty_sr = $tranDetRow['qty'];
                                                        }

                                                        $stockSql = "SELECT SUM((qty+qty_rr+qty_pr+qty_stin+qty_stot+qty_ajin+qty_ajot+qty_sr) - qty_si) AS qty_soh FROM r_stock WHERE cd_brnch='$lccd_brnch' AND cd_item='$lccd_item'";
                                                        $stockResult = mysqli_query($conn, $stockSql);
                                                        $stockResultCheck = mysqli_num_rows($stockResult);
                                                        if($stockResultCheck > 0) {
                                                            $stockRow = mysqli_fetch_assoc($stockResult);
                                                            $lnqty_soh = $stockRow['qty_soh'];

                                                            if($lnqty > ($lnqty_soh + $lnqty_sr)) {
                                                                $llcheck = false;
                                                                echo "Insufficient inventory of ".$lcnm_item.". "."Remaining Qty: ".number_format(($lnqty_soh + $lnqty_sr))."<br>";
                                                            }
                                                        } else {
                                                            $llcheck = false;
                                                            echo "Please set beginning balance for Item: ".$lcnm_item."<br>";
                                                        }
                                                    } else {

                                                    }
                                                }
                                            }
                                            if($llcheck) {
                                                $tranDetSql = "SELECT ty_tran, cd_stock, cd_item, qty FROM t_srd WHERE no_tran='$lcref'";
                                                $tranDetResult = mysqli_query($conn,$tranDetSql);
                                                $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                                                if($tranDetResultCheck > 0) {
                                                    while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
                                                        $lcty_tran = $tranDetRow['ty_tran'];
                                                        $lccd_stock = $tranDetRow['cd_stock'];
                                                        $lccd_item = $tranDetRow['cd_item'];
                                                        $lnqty = $tranDetRow['qty'];

                                                        $deleteTranDet = "DELETE FROM t_srd WHERE no_tran='$lcref' AND ty_tran='$lcty_tran' AND cd_stock='$lccd_stock' AND cd_item='$lccd_item'";
                                                            mysqli_query($conn, $deleteTranDet);
                                                        if($lnqty > 0) {
                                                            $updateStockRef = "UPDATE r_stock SET qty_si=qty_si - '$lnqty' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                                            mysqli_query($conn,$updateStockRef);
                                                        } else {
                                                            $updateStockRef = "UPDATE r_stock SET qty_sr=qty_sr + '$lnqty' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                                            mysqli_query($conn,$updateStockRef);
                                                        }
                                                    }
                                                }

                                                $lntot_gross= $lntot_disc= $lntot_net= 0.00;
                                                $tempDetSql = "SELECT * FROM t_tempsrd WHERE cd_user='$lccd_user' AND no_tran='$lcref'";
                                                $tempDetResult = mysqli_query($conn,$tempDetSql);
                                                $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                                                if($tempDetResultCheck > 0) {
                                                    while ($tempDetRow = mysqli_fetch_assoc($tempDetResult)) {
                                                        $lccd_stock = $tempDetRow['cd_stock'];
                                                        $lccd_item = $tempDetRow['cd_item'];
                                                        $lnqty = $tempDetRow['qty'];
                                                        $lnuprc = $tempDetRow['uprc'];
                                                        $lnamount = ($lnqty * $lnuprc);
                                                        $lndisc = $tempDetRow['disc'];
                                                        $lntotal = ($lnamount - $lndisc);

                                                        if($lnqty > 0) {
                                                            $lntot_qty = 0;
                                                            while($lntot_qty < $lnqty) {
                                                                $stocklst = "SELECT a.*, (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si AS qty_soh FROM r_stock a WHERE a.cd_brnch='$cd_brnch' AND a.cd_item='$lccd_item' AND (a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si > 0 ORDER BY a.t_add ASC";
                                                                $stockResult = mysqli_query($conn, $stocklst);
                                                                $stockResultCheck = mysqli_num_rows($stockResult);
                                                                if($stockResultCheck > 0) {
                                                                    $stockRow = mysqli_fetch_assoc($stockResult);
                                                                    $lnqty_soh = $stockRow['qty_soh'];
                                                                    $lccd_stock = $stockRow['no_tran'];
                                                                    
                                                                    if($lnqty_soh>=($lnqty-$lntot_qty)) {
                                                                        $lnqty_sr=($lnqty-$lntot_qty);
                                                                    } else {
                                                                        $lnqty_sr=$lnqty_soh;
                                                                    }
                                                                    $lnamount = $lnqty_sr * $lnuprc;
                                                                    $lntotal = $lnamount;
                                                                    $lntot_gross += $lnamount;
                                                                    $lntot_qty += $lnqty_sr;

                                                                    $insertTranDet = "INSERT INTO t_srd(ty_tran,no_tran,cd_stock,cd_item,qty,uprc,amount,total) VALUES('SI','$lcref','$lccd_stock','$lccd_item','$lnqty_sr','$lnuprc','$lnamount','$lntotal')";
                                                                    mysqli_query($conn, $insertTranDet);

                                                                    $updateStock = "UPDATE r_stock SET qty_si=qty_si + '$lnqty_sr', t_mod='$ltt_stamp' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                                                    mysqli_query($conn, $updateStock);
                                                                }
                                                            }
                                                            $lntotal = $lntotal - $lndisc;
                                                            $lntot_disc += $lndisc;

                                                            $updateTranDet = "UPDATE t_srd SET disc='$lndisc', total=total-'$lndisc' WHERE no_tran='$lcref' AND cd_stock='$lccd_stock' AND cd_item='$lccd_item'";
                                                            mysqli_query($conn, $updateTranDet);
                                                        } else {
                                                            $lntot_gross += $lnamount;
                                                            $lntot_disc += $lndisc;

                                                            $insertTranDet = "INSERT INTO t_srd(ty_tran, no_tran, cd_stock, cd_item, qty, uprc, amount, disc, total) VALUES('SR','$lcref','$lccd_stock','$lccd_item','$lnqty','$lnuprc','$lnamount', '$lndisc' , '$lntotal')";
                                                                    mysqli_query($conn,$insertTranDet);

                                                            $updateStock = "UPDATE r_stock SET qty_sr=qty_sr - '$lnqty' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                                            mysqli_query($conn, $updateStock);
                                                        }
                                                    }
                                                    $lntot_net = $lntot_gross - $lntot_disc;
                                                }
                                                $updateTran = "UPDATE t_sr SET dt_tran='$dt_tran', no_si='$no_si', cd_brnch='$cd_brnch', cd_cust='$lccd_cust', ref_no='$ref_no', remarks='$remarks', total='$lntot_gross', tot_disc='$lntot_disc', tot_net='$lntot_net', user_mod='$lccd_user', t_mod='$ltt_stamp' WHERE no_tran='$lcref'";
                                                mysqli_query($conn,$updateTran);

                                                $deleteTemp="DELETE FROM t_tempsr WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                                                mysqli_query($conn, $deleteTemp);
                                                $deleteTempDet="DELETE FROM t_tempsrd WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                                                mysqli_query($conn, $deleteTempDet);
                                            }
                                            $lcref ='';
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        echo "Reference ID could not be found.";
                    }
                } else {
                    echo "Temporary data is compromised.".$lcref;
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