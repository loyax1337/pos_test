<?php
if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch1']) && isset($_POST['cbocd_brnch2'])) {
    include_once 'includes/server.php';
    session_start();
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lcref = $_SESSION['ref'];
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
            $cd_brnch1 = (!empty($_POST['cbocd_brnch1']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch1'])) : '');
            $cd_brnch2 = (!empty($_POST['cbocd_brnch2']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch2'])) : '');
            //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');
            if(empty($dt_tran) || empty($cd_brnch1) || empty($cd_brnch2)) {
                echo 'Please input required fields completely.';
            } else {
                if($cd_brnch1 == $cd_brnch2) {
                    echo "Origin and Destination must not be the same.";
                } else {
                    $tranSql = "SELECT cd_brnch1, cd_brnch2 FROM t_st WHERE no_tran='$lcref'";
                    $tranResult = mysqli_query($conn, $tranSql);
                    $tranResultCheck = mysqli_num_rows($tranResult);
                    if($tranResultCheck > 0) {
                        $tranRow = mysqli_fetch_assoc($tranResult);
                        $lccd_brnch1 = $tranRow['cd_brnch1'];
                        $lccd_brnch2 = $tranRow['cd_brnch2'];

                        //Checking Inventory...
                        //Do something here..
                        
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

                                $updateStock = "UPDATE r_stock SET qty_stin=qty_stin - '$lnqty' WHERE cd_brnch='$lccd_brnch2' AND no_tran='$lcref' AND cd_item='$lccd_item'";
                                mysqli_query($conn,$updateStock);
                            }
                        }
                        $tranDet1Sql = "SELECT cd_item, cd_stock, qty FROM t_std1 WHERE no_tran='$lcref'";
                        $tranDet1Result = mysqli_query($conn,$tranDet1Sql);
                        $tranDet1ResultCheck = mysqli_num_rows($tranDet1Result);
                        if($tranDet1ResultCheck > 0) {
                            while($tranDet1Row = mysqli_fetch_assoc($tranDet1Result)) {
                                $lccd_item = $tranDet1Row['cd_item'];
                                $lccd_stock = $tranDet1Row['cd_stock'];
                                $lnqty = $tranDet1Row['qty'];

                                $deleteTranDet = "DELETE FROM t_std1 WHERE no_tran='$lcref' AND cd_stock='$lccd_stock' AND cd_item='$lccd_item'";
                                mysqli_query($conn, $deleteTranDet);

                                $updateStock = "UPDATE r_stock SET qty_stot=qty_stot - '$lnqty' WHERE cd_brnch='$lccd_brnch1' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                mysqli_query($conn, $updateStock);
                            }
                        }

                        $lntot_cst = 0.00;
                        $tempDetSql = "SELECT * FROM t_tempstd WHERE ty_tran='AC' AND no_tran='$lcref' AND cd_user='$lccd_user'";
                        $tempDetResult = mysqli_query($conn,$tempDetSql);
                        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                        if($tempDetResultCheck > 0) {
                            while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                                $lccd_item = $tempDetRow['cd_item'];
                                $lnqty = $tempDetRow['qty'];

                                $lnucst= $lnamt_cst= $lntot_qty = 0.00;
                                while($lntot_qty<$lnqty) {
                                    $stocklst = "SELECT a.*, (a.qty+a.qty_rr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot)-a.qty_si AS qty_soh FROM r_stock a WHERE a.cd_brnch='$cd_brnch1' AND a.cd_item='$lccd_item' AND (a.qty+a.qty_rr+a.qty_stin+a.qty_stot+a.qty_ajin+a.qty_ajot)-a.qty_si > 0 ORDER BY t_add";
                                    $stockResult = mysqli_query($conn,$stocklst);
                                    $stockResultCheck = mysqli_num_rows($stockResult);
                                    if($stockResultCheck > 0) {
                                        $stockRow = mysqli_fetch_assoc($stockResult);
                                        $lnqty_soh = $stockRow['qty_soh'];
                                        $lccd_stock = $stockRow['no_tran'];
                                        $lnucst = $stockRow['ucst'];

                                        if($lnqty_soh>=($lnqty-$lntot_qty)) {
                                            $lnqty_stot=($lnqty-$lntot_qty);
                                        } else {
                                            $lnqty_stot=$lnqty_soh;
                                        }
                                        $lnamt_cst = $lnqty_stot * $lnucst;
                                        $lntot_cst = $lntot_cst + $lnamt_cst;
                                        $lntot_qty = $lntot_qty + $lnqty_stot;

                                        $lnqty_stot1 = ($lnqty_stot * -1);
                                        $lnamt_cst1 = $lnqty_stot1 * $lnucst;

                                        $updateStock = "UPDATE r_stock SET qty_stot=qty_stot + '$lnqty_stot1', t_mod='$ltt_stamp' WHERE cd_brnch='$cd_brnch1' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                        mysqli_query($conn,$updateStock);

                                        $insertTranDet = "INSERT INTO t_std1(no_tran,cd_stock,cd_item,qty,ucst,amt_cst) VALUES('$lcref','$lccd_stock','$lccd_item','$lnqty_stot1','$lnucst','$lnamt_cst1')";
                                        mysqli_query($conn,$insertTranDet);
                                    }
                                }

                                $insertTranDet = "INSERT INTO t_std(no_tran,cd_item,qty,ucst,amt_cst) VALUES('$lcref','$lccd_item','$lnqty','$lnucst','$lnamt_cst')";
                                mysqli_query($conn,$insertTranDet);

                                $stockSql = "SELECT cd_brnch, no_tran, cd_item FROM r_stock WHERE cd_brnch='$cd_brnch2' AND no_tran='$lcref' AND cd_item='$lccd_item'";
                                $stockResult = mysqli_query($conn,$stockSql);
                                $stockResultCheck = mysqli_num_rows($stockResult);
                                if($stockResultCheck > 0) {
                                    $stockQuery = "UPDATE r_stock SET qty_stin=qty_stin+'$lnqty',ucst='$lnucst' WHERE cd_brnch='$cd_brnch2' AND no_tran='$lcref' AND cd_item='$lccd_item'";
                                } else {
                                    $stockQuery = "INSERT INTO r_stock(cd_brnch,no_tran,cd_item,qty_stin,ucst,cd_user,t_add) VALUES('$cd_brnch2','$lcref','$lccd_item','$lnqty','$lnucst','$lccd_user','$ltt_stamp')";
                                }
                                mysqli_query($conn,$stockQuery);
                            }
                        }
                        $updateSql = "UPDATE t_st SET dt_tran='$dt_tran',ref_no='$ref_no',cd_brnch1='$cd_brnch1',cd_brnch2='$cd_brnch2',tot_cst='$lntot_cst',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE no_tran='$lcref'";
                        mysqli_query($conn,$updateSql);

                        $deleteTemp="DELETE FROM t_tempst WHERE ty_tran='AC' AND no_tran='$lcref' AND cd_user='$lccd_user'";
                        mysqli_query($conn, $deleteTemp);
                        $deleteTempDet="DELETE FROM t_tempstd WHERE ty_tran='AC' AND no_tran='$lcref' AND cd_user='$lccd_user'";
                        mysqli_query($conn, $deleteTempDet);
                        $lcref ='';
                    } else {
                        echo "The reference ID could not be found.";
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
?>