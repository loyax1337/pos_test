<?php
session_start();
if(isset($_SESSION['id'])) {
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    $lccd_user = $_SESSION['id'];
    $lcref = $_SESSION['ref'];

    if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch'])) {
        include_once 'includes/server.php';
        $lccd_menu = '033';

        $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0) {
            $row = mysqli_fetch_assoc($result);
            $llaction = $row['edit'];
            if($llaction=='1') {
                $dt_tran = $_POST['txtdt_tran'];
                $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
                $cd_brnch = (!empty($_POST['cbocd_brnch']) ? mysqli_real_escape_string($conn, base64_decode($_POST['cbocd_brnch'])) : '');
                //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

                date_default_timezone_set('Asia/Manila');
                $ltt_stamp = date('Y-m-d H:i:s');
                if(empty($dt_tran) || empty($cd_brnch)) {
                    echo 'Please input required fields completely.';
                } else {
                    $tranSql = "SELECT no_tran, cd_brnch FROM t_rr WHERE no_tran='$lcref'";
                    $tranResult = mysqli_query($conn, $tranSql);
                    $tranResultCheck = mysqli_num_rows($tranResult);
                    if($tranResultCheck > 0) {
                        $tranRow = mysqli_fetch_assoc($tranResult);
                        $lccd_brnch = $tranRow['cd_brnch'];

                        $tranDetSql = "SELECT cd_item, qty FROM t_rrd WHERE no_tran='$lcref'";
                        $tranDetResult = mysqli_query($conn,$tranDetSql);
                        $tranDetResultCheck = mysqli_num_rows($tranDetResult);
                        if($tranDetResultCheck > 0) {
                            while($tranDetRow = mysqli_fetch_assoc($tranDetResult)) {
                                $lccd_item = $tranDetRow['cd_item'];
                                $lnqty = $tranDetRow['qty'];

                                $deleteTranDet = "DELETE FROM t_rrd WHERE no_tran='$lcref' AND cd_item='$lccd_item'";
                                mysqli_query($conn, $deleteTranDet);

                                $updateStockRef = "UPDATE r_stock SET qty_rr=qty_rr-'$lnqty' WHERE cd_brnch='$lccd_brnch' AND no_tran='$lcref' AND cd_item='$lccd_item'";
                                mysqli_query($conn,$updateStockRef);
                            }
                        }

                        $lntot_cst= $lntot_prc= 0.00;
                        $tempDetSql = "SELECT * FROM t_temprrd WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                        $tempDetResult = mysqli_query($conn,$tempDetSql);
                        $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                        if($tempDetResultCheck > 0) {
                            while($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                                $lccd_item = $tempDetRow['cd_item'];
                                $lnqty = $tempDetRow['qty'];
                                $lnucst = $tempDetRow['ucst'];
                                $lnamt_cst = ($lnqty * $lnucst);
                                $lnuprc = $tempDetRow['uprc'];
                                $lnamt_prc = ($tempDetRow['qty'] * $tempDetRow['uprc']);
                                $lntot_cst += $lnamt_cst;
                                $lntot_prc += $lnamt_prc;
                                $insertTranDet = "INSERT INTO t_rrd(no_tran,cd_item,qty,ucst,amt_cst,uprc) VALUES('$lcref','$lccd_item','$lnqty','$lnucst','$lnamt_cst','$lnuprc')";
                                mysqli_query($conn,$insertTranDet);

                                $stockSql = "SELECT cd_brnch, no_tran, cd_item FROM r_stock WHERE cd_brnch='$cd_brnch' AND no_tran='$lcref' AND cd_item='$lccd_item'";
                                $stockResult = mysqli_query($conn,$stockSql);
                                $stockResultCheck = mysqli_num_rows($stockResult);
                                if($stockResultCheck > 0) {
                                    $stockQuery = "UPDATE r_stock SET qty_rr=qty_rr+'$lnqty',ucst='$lnucst' WHERE cd_brnch='$cd_brnch' AND no_tran='$lcref' AND cd_item='$lccd_item'";
                                } else {
                                    $stockQuery = "INSERT INTO r_stock(cd_brnch,no_tran,cd_item,qty_rr,ucst,cd_user,t_add) VALUES('$cd_brnch','$lcref','$lccd_item','$lnqty','$lnucst','$lccd_user','$ltt_stamp')";
                                }
                                mysqli_query($conn,$stockQuery);
                            }
                        }
                        $updateTran = "UPDATE t_rr SET cd_brnch='$cd_brnch',dt_tran='$dt_tran',ref_no='$ref_no',tot_cst='$lntot_cst',tot_prc='$lntot_prc',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE no_tran='$lcref'";
                        mysqli_query($conn,$updateTran);

                        $deleteTemp="DELETE FROM t_temprr WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                        mysqli_query($conn, $deleteTemp);
                        $deleteTempDet="DELETE FROM t_temprrd WHERE no_tran='$lcref' AND cd_user='$lccd_user'";
                        mysqli_query($conn, $deleteTempDet);
                    } else {
                        echo "Reference ID could not be found.";
                    }
                    $lcref ='';
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