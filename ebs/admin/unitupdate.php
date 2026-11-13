<?php
if(isset($_POST['txtnm_unit']) && isset($_POST['edtdesc_unit']) && isset($_POST['cbocd_brnch']) && isset($_POST['txtuprc']) && isset($_POST['txtdt_eff']) && isset($_POST['fileimg_src'])) {
    include_once 'includes/server.php';
    session_start();
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lcref = $_SESSION['ref'];
    $lccd_menu = '011';

    $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $llaction = $row['edit'];
        if($llaction=='1') {
            $nm_unit = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_unit']));
            $desc_unit = trim(mysqli_real_escape_string($conn,$_POST['edtdesc_unit']));

            $cd_brnch = (!empty($_POST['cbocd_brnch']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_brnch'])) : '');
            $uprc = (empty($_POST['txtuprc']) ? 0.00 : (($_POST['txtuprc'] <= 0.00) ? 0.00 : $_POST['txtuprc']));
            $dt_eff = $_POST['txtdt_eff'];
            $img_src = $_POST['fileimg_src'];

            //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');
            if (empty($nm_unit)){
                echo 'Please input required fields completely and valid.';
            } else {
                $updateTran = "UPDATE r_unit SET nm_unit='$nm_unit',desc_unit='$desc_unit',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_unit='$lcref'";
                mysqli_query($conn, $updateTran);

                if(!empty($img_src)) {
                    $sql = "UPDATE r_unit SET img_src='$img_src' WHERE cd_unit='$lcref'";
                    mysqli_query($conn,$sql);
                }
                //Price update
                if(($uprc > 0) && !empty($dt_eff)) {
                    $updateTran = "UPDATE r_unit SET uprc='$uprc', dt_eff='$dt_eff' WHERE cd_unit='$lcref'";
                    mysqli_query($conn, $updateTran);

                    if(!empty($cd_brnch)) {
                        $uprcSql = "SELECT dt_eff FROM r_uprc WHERE dt_eff='$dt_eff' AND cd_unit='$lcref' AND cd_brnch='$cd_brnch'";
                        $urpcResult = mysqli_query($conn,$uprcSql);
                        $uprcResultCheck = mysqli_num_rows($urpcResult);
                        if($uprcResultCheck <= 0) {
                            $insertPriceHistory = "INSERT INTO r_uprc (cd_brnch, dt_eff, cd_unit, uprc, t_add, user_add) VALUES('$cd_brnch','$dt_eff','$lcref','$uprc','$ltt_stamp','$lccd_user')";
                            mysqli_query($conn, $insertPriceHistory);
                        }
                    } else {
                        $brnchSql = "SELECT cd_brnch FROM r_brnch WHERE status='A'";
                        $brnchResult = mysqli_query($conn, $brnchSql);
                        $brnchResultCheck = mysqli_num_rows($brnchResult);
                        if($brnchResultCheck > 0) {
                            while($brnchRow = mysqli_fetch_assoc($brnchResult)) {
                                $lccd_brnch = $brnchRow['cd_brnch'];

                                $uprcSql = "SELECT dt_eff FROM r_uprc WHERE dt_eff='$dt_eff' AND cd_unit='$lcref' AND cd_brnch='$lccd_brnch'";
                                $urpcResult = mysqli_query($conn,$uprcSql);
                                $uprcResultCheck = mysqli_num_rows($urpcResult);
                                if($uprcResultCheck <= 0) {
                                    $insertPriceHistory = "INSERT INTO r_uprc (cd_brnch, dt_eff, cd_unit, uprc, t_add, user_add) VALUES('$lccd_brnch','$dt_eff','$lcref','$uprc','$ltt_stamp','$lccd_user')";
                                    mysqli_query($conn, $insertPriceHistory);
                                }
                            }
                        }
                    }
                }
                //Stock update
                if(!empty($cd_brnch) && ($qty)>0 && ($ucst)>0) {
                    $sql = "SELECT cd_brnch,no_tran,cd_unit FROM r_stock WHERE cd_brnch='$cd_brnch' AND no_tran='$lcref' AND cd_unit='$lcref'";
                    $result = mysqli_query($conn,$sql);
                    $resultCheck = mysqli_num_rows($result);
                    if($resultCheck > 0) {
                        $sql = "UPDATE r_stock SET qty='$qty', ucst='$ucst', t_mod='$ltt_stamp' WHERE cd_brnch='$cd_brnch' AND no_tran='$lcref' AND cd_unit='$lcref'";
                    } else {
                        $sql = "INSERT INTO r_stock(cd_brnch,no_tran,cd_unit,qty,ucst,cd_user,t_add) VALUES('$cd_brnch','$lcref','$lcref','$qty','$ucst','$lccd_user','$ltt_stamp')";
                    }
                    mysqli_query($conn,$sql);
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
?>