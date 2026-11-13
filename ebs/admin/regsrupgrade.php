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
?>
<?php
if(isset($_POST['txtdt_tran']) && isset($_POST['txtref_no']) && isset($_POST['cbocd_brnch']) && isset($_POST['cboty_pay1']) && isset($_POST['txtamt_pay1']) && isset($_POST['txtref_pay1']) && isset($_POST['edtremarks'])) {
    include_once 'includes/server.php';
    session_start();
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lccd_brnch = $_SESSION['branch'];
    $lccd_menu = '029';

    $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $llaction = $row['add'];
        if($llaction=='1') {
            if(isset($_SESSION['id'])) {
                $dt_tran = $_POST['txtdt_tran'];
                $ref_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtref_no']));
                //$cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');
                $cd_brnch = $lccd_brnch;
                $ty_pay1 = (!empty($_POST['cboty_pay1']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cboty_pay1'])) : '');
                $amt_pay1 = (empty($_POST['txtamt_pay1']) ? 0.00 : $_POST['txtamt_pay1']);
                $ref_pay1 = strtoupper(mysqli_real_escape_string($conn, $_POST['txtref_pay1']));

                $remarks = ltrim(mysqli_real_escape_string($conn,$_POST['edtremarks']));
                date_default_timezone_set('Asia/Manila');
                $ltt_stamp = date('Y-m-d H:i:s');
                if(empty($dt_tran) || empty($cd_brnch)){
                    echo 'Please input required fields completely.';
                } else {
                    if($amt_pay1 >= 0) {
                        $tempSql = "SELECT no_si, cd_cust FROM t_tempsr WHERE cd_user='$lccd_user' AND no_tran IS NULL";
                        $tempResult = mysqli_query($conn, $tempSql);
                        $tempResultCheck = mysqli_num_rows($tempResult);
                        if($tempResultCheck > 0) {
                            $tempRow = mysqli_fetch_assoc($tempResult);
                            $no_si = $tempRow['no_si'];
                            $cd_cust = $tempRow['cd_cust'];

                            $llcheck = true;
                            //Check Inventory...
                            $lntot_net = 0.00;
                            $tempDetSql = "SELECT a.*, b.nm_item, b.serial_no, b.imei1_no, b.imei2_no FROM t_tempsrd a LEFT JOIN r_item b ON a.cd_item=b.cd_item WHERE a.cd_user='$lccd_user' AND a.no_tran IS NULL";
                            $tempDetResult = mysqli_query($conn,$tempDetSql);
                            $tempDetResultCheck = mysqli_num_rows($tempDetResult);
                            if($tempDetResultCheck > 0) {
                                while ($tempDetRow=mysqli_fetch_assoc($tempDetResult)) {
                                    $lccd_item = $tempDetRow['cd_item'];
                                    $lcnm_item = $tempDetRow['nm_item'];
                                    $lnqty = $tempDetRow['qty'];
                                    $lnuprc = $tempDetRow['uprc'];
                                    $lnamount = ($lnqty * $lnuprc);
                                    $lndisc = $tempDetRow['disc'];
                                    $lntotal = $lnamount - $lndisc;

                                    $lntot_net += $lntotal;
                                    if($lnqty > 0) {
                                        $stockSql = "SELECT SUM((qty+qty_rr+qty_pr+qty_stin+qty_stot+qty_ajin+qty_ajot+qty_sr) - qty_si) AS qty_soh FROM r_stock WHERE cd_brnch='$cd_brnch' AND cd_item='$lccd_item'";
                                        $stockResult = mysqli_query($conn, $stockSql);
                                        $stockResultCheck = mysqli_num_rows($stockResult);
                                        if($stockResultCheck > 0) {
                                            $stockRow = mysqli_fetch_assoc($stockResult);
                                            $lnqty_soh = $stockRow['qty_soh'];

                                            if($lnqty > $lnqty_soh) {
                                                $llcheck = false;
                                                echo "Insufficient inventory of ".$lcnm_item.". "."Remaining Qty: ".number_format($lnqty_soh)."<br>";
                                            }
                                        } else {
                                            $llcheck = false;
                                            echo "Please set beginning balance for Item: ".$lcnm_item."<br>";
                                        }
                                    }
                                }
                                if($lntot_net <= 0) {
                                    $llcheck = false;
                                    echo "No additional payment can be applied on this transaction.";
                                }
                                if(!empty($ty_pay1) && $ty_pay1!='PM00000001') {
                                    if($amt_pay1 > $lntot_net || empty($ref_pay1)) {
                                        $llcheck = false;
                                        echo "Please enter a valid Amount and Reference No. for online transaction.";
                                    }
                                } else {
                                    if($amt_pay1 < $lntot_net) {
                                        $llcheck = false;
                                        echo "Please complete the additional payment.";
                                    }
                                }
                            } else {
                                $llcheck = false;
                                echo "SR Item details cannot be empty.";
                            }

                            if($llcheck) {
                                $status = 'U';
                                $prefix = 'SR';
                                $seekDocnoResultCheck = 1;
                                do {
                                    $no_doc = docno($prefix,$conn);
                                    
                                    $seekDocno = "SELECT no_tran FROM t_sr WHERE no_tran='$no_doc'";
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
                                    $lntot_gross= $lntot_disc= $lntot_net= 0.00;
                                    $tempDetSql = "SELECT * FROM t_tempsrd WHERE cd_user='$lccd_user' AND no_tran IS NULL";
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

                                                        $insertTranDet = "INSERT INTO t_srd(ty_tran,no_tran,cd_stock,cd_item,qty,uprc,amount,total) VALUES('SI','$no_doc','$lccd_stock','$lccd_item','$lnqty_sr','$lnuprc','$lnamount','$lntotal')";
                                                        mysqli_query($conn, $insertTranDet);

                                                        $updateStock = "UPDATE r_stock SET qty_si=qty_si + '$lnqty_sr', t_mod='$ltt_stamp' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                                        mysqli_query($conn, $updateStock);
                                                    }
                                                }
                                                $lntotal = $lntotal - $lndisc;
                                                $lntot_disc += $lndisc;

                                                $updateTranDet = "UPDATE t_srd SET disc='$lndisc', total=total-'$lndisc' WHERE no_tran='$no_doc' AND cd_stock='$lccd_stock' AND cd_item='$lccd_item'";
                                                mysqli_query($conn, $updateTranDet);
                                            } else {
                                                $lntot_gross += $lnamount;
                                                $lntot_disc += $lndisc;

                                                $stockSql = "SELECT no_tran FROM r_stock WHERE cd_brnch='$cd_brnch' AND cd_item='$lccd_item' ORDER BY t_add DESC";
                                                $stockResult = mysqli_query($conn, $stockSql);
                                                $stockResultCheck = mysqli_num_rows($stockResult);
                                                if($stockResultCheck > 0) {
                                                    $stockRow = mysqli_fetch_assoc($stockResult);
                                                    $lccd_stock = $stockRow['no_tran'];
                                                    $insertTranDet = "INSERT INTO t_srd(ty_tran,no_tran, cd_stock, cd_item, qty, uprc, amount, disc, total) VALUES('SR','$no_doc','$lccd_stock','$lccd_item','$lnqty','$lnuprc','$lnamount', '$lndisc' , '$lntotal')";
                                                    mysqli_query($conn,$insertTranDet);

                                                    $updateStock = "UPDATE r_stock SET qty_sr=qty_sr - '$lnqty' WHERE cd_brnch='$cd_brnch' AND no_tran='$lccd_stock' AND cd_item='$lccd_item'";
                                                    mysqli_query($conn, $updateStock);
                                                }
                                            }
                                        }
                                        $lntot_net = $lntot_gross - $lntot_disc;
                                    }
                                    $insertTran = "INSERT INTO t_sr(no_tran,dt_tran,ref_no,no_si,cd_brnch,cd_cust,remarks,total,tot_disc,tot_net,user_add,t_add,status) VALUES('$no_doc','$dt_tran','$ref_no','$no_si','$cd_brnch','$cd_cust','$remarks','$lntot_gross','$lntot_disc','$lntot_net','$lccd_user','$ltt_stamp','$status')";
                                    mysqli_query($conn,$insertTran);

                                    //Update Cash Register...
                                    $updateCreg1 = "UPDATE t_creg SET amt_sales=amt_sales+'$amt_pay1' WHERE cd_brnch='$cd_brnch' AND ty_pay='$ty_pay1' AND status='A'";
                                    mysqli_query($conn,$updateCreg1);

                                    $deleteTemp="DELETE FROM t_tempsr WHERE cd_user='$lccd_user' AND no_tran IS NULL";
                                    mysqli_query($conn, $deleteTemp);
                                    $deleteTempDet="DELETE FROM t_tempsrd WHERE cd_user='$lccd_user' AND no_tran IS NULL";
                                    mysqli_query($conn, $deleteTempDet);
                                    break;
                                }
                            }
                        } else {
                            echo "Temporary data is compromised.";
                        }
                    } else {
                        echo "Please enter a valid Payment amount.";
                    }
                }
            } else {
                echo 'Session Expired. Please log in.';
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