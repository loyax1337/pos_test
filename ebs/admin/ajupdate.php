<?php
if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch'])) {
    include_once 'includes/server.php';
    session_start();
    
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lcref = $_SESSION['ref'];
    $lccd_menu = '020';

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
            if(empty($dt_tran) || empty($cd_brnch)) {
                echo 'Please input required fields completely.';
            } else {
                $tranSql = "SELECT cd_brnch FROM t_aj WHERE no_tran='$lcref'";
                $tranResult = mysqli_query($conn, $tranSql);
                $tranResultCheck = mysqli_num_rows($tranResult);
                if($tranResultCheck > 0) {
                    $tranRow = mysqli_fetch_assoc($tranResult);
                    $lccd_brnch = $tranRow['cd_brnch'];

                    $tranDetSql = "SELECT cd_stock, cd_item, qty FROM t_ajd WHERE no_tran='$lcref'";
                    $tranDetResult = mysqli_query($conn,$tranDetSql);
                    $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                    if($tranDetResultCheck > 0) {
                        while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
                            $lccd_stock = $tranDetRow['cd_stock'];
                            $lccd_item = $tranDetRow['cd_item'];
                            $lnqty = $tranDetRow['qty'];

                            $deleteTranDet = "DELETE FROM t_ajd WHERE no_tran='$lcref' AND cd_stock='$lccd_stock' AND cd_item='$lccd_item'";
                            mysqli_query($conn, $deleteTranDet);

                            if($lnqty > 0) {
                                $updateStock = "UPDATE r_stock SET qty_ajin=qty_ajin-'$lnqty' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                            } else {
                                $updateStock = "UPDATE r_stock SET qty_ajot=qty_ajot-'$lnqty' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                            }
                            mysqli_query($conn,$updateStock);
                        }
                    }

                    $lntot_cst = 0.00;
                    $tempDetSql = "SELECT a.cd_item, SUM(a.qty) AS tot_qtyaj, a.ucst FROM t_tempajd a WHERE a.no_tran='$lcref' AND a.cd_user='$lccd_user' GROUP BY a.cd_item";
                    $tempDetResult = mysqli_query($conn,$tempDetSql);
                    $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                    if($tempDetResultCheck > 0) {
                        while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                            $lccd_item = $tempDetRow['cd_item'];
                            $lnqty = $tempDetRow['tot_qtyaj'];
                            $lnucst = $tempDetRow['ucst'];
                            $lnamt_cst = ($lnqty * $lnucst);

                            if($lnqty < 0) {
                                $lnqty = abs($lnqty);
                                $lntot_qty = 0.00;
                                while($lntot_qty < $lnqty) {
                                    $stocklst = "SELECT a.*, (a.qty+a.qty_rr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot)-a.qty_si AS qty_soh FROM r_stock a WHERE a.cd_brnch='$cd_brnch' AND a.cd_item='$lccd_item' AND (a.qty+a.qty_rr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot)-a.qty_si > 0 ORDER BY t_add";
                                    $stockResult = mysqli_query($conn,$stocklst);
                                    $stockResultCheck = mysqli_num_rows($stockResult);
                                    if($stockResultCheck > 0) {
                                        $stockRow = mysqli_fetch_assoc($stockResult);
                                        $lnqty_soh = $stockRow['qty_soh'];
                                        $lccd_stock = $stockRow['no_tran'];
                                        $lnucst = $stockRow['ucst'];

                                        if($lnqty_soh >= ($lnqty - $lntot_qty)) {
                                            $lnqty_ajot = ($lnqty - $lntot_qty);
                                        } else {
                                            $lnqty_ajot = $lnqty_soh;
                                        }
                                        $lnqty_ajot1 = ($lnqty_ajot * -1);
                                        $lnamt_cst = $lnqty_ajot1 * $lnucst;
                                        $lntot_cst += $lnamt_cst;
                                        $lntot_qty += $lnqty_ajot;

                                        $updateStock = "UPDATE r_stock SET qty_ajot=qty_ajot+$lnqty_ajot1, t_mod='$ltt_stamp' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                        mysqli_query($conn,$updateStock);

                                        $insertTranDet = "INSERT INTO t_ajd(no_tran,cd_stock,cd_item,qty,ucst,amt_cst) VALUES('$lcref','$lccd_stock','$lccd_item','$lnqty_ajot1','$lnucst','$lnamt_cst')";
                                        mysqli_query($conn,$insertTranDet);
                                    }
                                }
                            } else {
                                $lntot_cst = ($lntot_cst + $lnamt_cst);
                                $insertSql = "INSERT INTO t_ajd(no_tran,cd_stock,cd_item,qty,ucst,amt_cst) VALUES('$lcref','$lcref','$lccd_item','$lnqty','$lnucst','$lnamt_cst')";
                                mysqli_query($conn,$insertSql);

                                $stockSql = "SELECT cd_brnch, no_tran, cd_item FROM r_stock WHERE cd_brnch='$cd_brnch' AND no_tran='$lcref' AND cd_item='$lccd_item'";
                                $stockResult = mysqli_query($conn,$stockSql);
                                $stockResultCheck = mysqli_num_rows($stockResult);
                                if($stockResultCheck > 0) {
                                    $stockQuery = "UPDATE r_stock SET qty_ajin=qty_ajin+'$lnqty',ucst='$lnucst' WHERE cd_brnch='$cd_brnch' AND no_tran='$lcref' AND cd_item='$lccd_item'";
                                } else {
                                    $stockQuery = "INSERT INTO r_stock(cd_brnch,no_tran,cd_item,qty_ajin,ucst,cd_user,t_add) VALUES('$cd_brnch','$lcref','$lccd_item','$lnqty','$lnucst','$lccd_user','$ltt_stamp')";
                                }
                                mysqli_query($conn,$stockQuery);
                            }
                        }
                    }
                    $updateSql = "UPDATE t_aj SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch='$cd_brnch',tot_cst='$lntot_cst',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE no_tran='$lcref'";
                    mysqli_query($conn,$updateSql);

                    $deleteTemp="DELETE FROM t_tempaj WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                    mysqli_query($conn, $deleteTemp);
                    $deleteTempDet="DELETE FROM t_tempajd WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                    mysqli_query($conn, $deleteTempDet);
                    $lcref ='';
                } else {
                    echo "The reference ID could not be found.";
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
?>