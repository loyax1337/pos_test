<?php
    session_start();
    if(isset($_SESSION['id'])) {
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        $lccd_brnch = $_SESSION['branch'];
        $lcref = $_SESSION['ref'];

        if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch1']) && isset($_POST['cbocd_brnch2'])) {
            include_once 'includes/server.php';
            $lccd_menu = '017';

            $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0) {
                $row = mysqli_fetch_assoc($result);
                $llaction = $row['edit'];
                if($llaction=='1') {
                    $dt_tran = $_POST['txtdt_tran'];
                    $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
                    $cd_brnch1 = (!empty($_POST['cbocd_brnch1']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch1'])) : '');
                    $cd_brnch2 = (!empty($_POST['cbocd_brnch2']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch2'])) : '');
                    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

                    date_default_timezone_set('Asia/Manila');
                    $ltt_stamp = date('Y-m-d H:i:s');
                    if(empty($dt_tran) || empty($cd_brnch1) || empty($cd_brnch2)) {
                        echo 'Please input required fields completely.';
                    } else {
                        if($lccd_brnch != $cd_brnch1) {
                            echo "You have no access to modify this record.";
                        } else {
                            if($cd_brnch1 == $cd_brnch2) {
                                echo "Origin and Destination must not be the same.";
                            } else {
                                $tranSql = "SELECT cd_brnch1, cd_brnch2, status FROM t_st WHERE no_tran='$lcref'";
                                $tranResult = mysqli_query($conn, $tranSql);
                                $tranResultCheck = mysqli_num_rows($tranResult);
                                if($tranResultCheck > 0) {
                                    $tranRow = mysqli_fetch_assoc($tranResult);
                                    $lccd_brnch1 = $tranRow['cd_brnch1'];
                                    $lccd_brnch2 = $tranRow['cd_brnch2'];
                                    $lcstatus = $tranRow['status'];

                                    switch($lcstatus) {
                                    case 'V':
                                        echo "Voided transaction can no longer be modified.";
                                        break;
                                    case 'A':
                                        echo "Approved Transaction can no longer be modified.";
                                        break;
                                    default:
                                        //Checking Inventory...
                                        $llcheck = true;
                                        $tempDetSql = "SELECT a.*, b.nm_item FROM t_tempstd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.ty_tran='KL' AND a.no_tran='$lcref'";
                                        $tempDetResult = mysqli_query($conn,$tempDetSql);
                                        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                                        if($tempDetResultCheck > 0) {
                                            while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                                                $lccd_item = $tempDetRow['cd_item'];
                                                $lcnm_item = $tempDetRow['nm_item'];
                                                $lnqty = $tempDetRow['qty'];

                                                $lnqty_st = 0;
                                                $tranDetSql = "SELECT qty FROM t_std WHERE no_tran='$lccd_user' AND cd_item='$lccd_item'";
                                                $tranDetResult = mysqli_query($conn, $tranDetSql);
                                                $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                                                if($tranDetResultCheck > 0) {
                                                    $tranDetRow = mysqli_fetch_assoc($tranDetResult);
                                                    $lnqty_st = $tranDetRow['qty'];
                                                } 

                                                $stockSql = "SELECT a.*, SUM((a.qty+a.qty_rr+a.qty_pr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot+a.qty_sr)-a.qty_si) AS qty_soh FROM r_stock a WHERE a.cd_brnch='$lccd_brnch1' AND a.cd_item='$lccd_item'";
                                                $stockResult = mysqli_query($conn,$stockSql);
                                                $stockResultCheck = mysqli_num_rows($stockResult);
                                                if($stockResultCheck > 0) {
                                                    $stockRow = mysqli_fetch_assoc($stockResult);
                                                    $lnqty_soh = $stockRow['qty_soh'];
                                                    if($lnqty > ($lnqty_soh + $lnqty_st)) {
                                                        $llcheck = false;
                                                        echo "Insufficient inventory. ".$lcnm_item." "."Remaining Qty: ".number_format(($lnqty_soh + $lnqty_st))."<br>";
                                                    }
                                                } else {
                                                    $llcheck = false;
                                                    echo "Please set beginning balance. ".$lcnm_item."<br>";
                                                }
                                            }
                                        } else {
                                            $llcheck = false;
                                            echo "ST Item details cannot be empty.";
                                        }
                                        if($llcheck) {
                                            //Retrieving Inventory...
                                            $tranDetSql = "SELECT cd_item, qty FROM t_std WHERE no_tran='$lcref'";
                                            $tranDetResult = mysqli_query($conn,$tranDetSql);
                                            $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                                            if($tranDetResultCheck > 0) {
                                                while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
                                                    $lccd_item = $tranDetRow['cd_item'];
                                                    $lnqty = $tranDetRow['qty'];

                                                    $deleteTranDet = "DELETE FROM t_std WHERE no_tran='$lcref' AND cd_item='$lccd_item'";
                                                    mysqli_query($conn, $deleteTranDet);
                                                }
                                            }

                                            $lntot_cst = 0.00;
                                            $tempDetSql = "SELECT * FROM t_tempstd WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                                            $tempDetResult = mysqli_query($conn,$tempDetSql);
                                            $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                                            if($tempDetResultCheck > 0) {
                                                while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                                                    $lccd_item = $tempDetRow['cd_item'];
                                                    $lnqty = $tempDetRow['qty'];
                                                    $lnucst = $tempDetRow['ucst'];
                                                    $lnamt_cst = ($lnqty * $lnucst);

                                                    $insertTranDet = "INSERT INTO t_std(no_tran,cd_item,qty,ucst,amt_cst) VALUES('$lcref','$lccd_item','$lnqty','$lnucst','$lnamt_cst')";
                                                    mysqli_query($conn,$insertTranDet);
                                                }
                                            }
                                            $updateTran = "UPDATE t_st SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch1='$cd_brnch1',cd_brnch2='$cd_brnch2',tot_cst='$lntot_cst',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE no_tran='$lcref'";
                                            mysqli_query($conn, $updateTran);

                                            $deleteTemp="DELETE FROM t_tempst WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                                            mysqli_query($conn, $deleteTemp);
                                            $deleteTempDet="DELETE FROM t_tempstd WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                                            mysqli_query($conn, $deleteTempDet);
                                        }
                                        break;
                                    }
                                    $lcref ='';
                                } else {
                                    echo "The reference ID could not be found.";
                                }
                            }
                        }
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
        echo "Session Expired. Please login again.";
    }
?>