<?php
   if(isset($_POST['cbocd_categ']) && isset($_POST['cbocd_model']) && isset($_POST['cbocd_supp']) && isset($_POST['cbocd_bcode']) && isset($_POST['cbocd_jwcode']) && isset($_POST['cbocd_roms']) && isset($_POST['cbocd_rs']) && isset($_POST['cbocd_ct']) && isset($_POST['cbocd_grms']) && isset($_POST['cbocd_uprc'])) { 
    include_once 'includes/server1.php';
    session_start();
    $lccd_brnch = $_SESSION['branch'];
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];
    $lcref = $_SESSION['ref'];
    $lccd_menu = '013';

    $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck > 0) {
        $row = mysqli_fetch_assoc($result);
        $llaction = $row['edit'];
        if($llaction=='1') {
                $cd_categ = (!empty($_POST['cbocd_categ']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_categ'])) : '');
                $cd_model = (!empty($_POST['cbocd_model']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_model'])) : '');
                $cd_supp = (!empty($_POST['cbocd_supp']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_supp'])) : '');
                $cd_bcode = strtoupper($_POST['cbocd_bcode']);
                $cd_jwcode = strtoupper($_POST['cbocd_jwcode']);
                $cd_roms = strtoupper($_POST['cbocd_roms']);
                $cd_rs = strtoupper($_POST['cbocd_rs']);
                $cd_ct = strtoupper($_POST['cbocd_ct']);
                $cd_grms = strtoupper($_POST['cbocd_grms']);
                $cd_uprc = strtoupper($_POST['cbocd_uprc']);

            //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');
            if(empty($cd_categ)) {
                echo "Please select a Category.";
            } else {
                if(empty($cd_model)) {
                    echo "Please select a Brand.";
                } else {
                    if(empty($cd_supp)) {
                        echo "Please select a Supplier.";
                    } else {
                        $categSql = "SELECT a.cd_categ, a.nm_categ FROM r_categ a WHERE a.cd_categ='$cd_categ'";
                        $categResult = mysqli_query($conn, $categSql);
                        $categResultCheck = mysqli_num_rows($categResult);
                        if($categResultCheck <= 0) {
                            echo "Selected Category could not be found.";
                        } else {    
                            $unitSql = "SELECT a.cd_item, a.jw_code, a.nm_item,a.cd_categ,a.cd_model,a.rs_size,a.carat_no,a.grams,a.uprc,b.nm_model FROM r_itemjwl a LEFT JOIN r_model b ON a.cd_model=b.cd_model WHERE a.cd_item='$lcref'";
                            $unitResult = mysqli_query($conn, $unitSql);
                            $unitResultCheck = mysqli_num_rows($unitResult);
                            if($unitResultCheck <= 0) {
                                echo "Selected Item could not be found.";
                            } else {
                                $unitRow = mysqli_fetch_assoc($unitResult);
                                    $lccd_item = $unitRow['cd_item'];
                                    $lcjw_code = $unitRow['jw_code'];
                                    $lcnm_item = $unitRow['nm_item'];
                                    $lccd_categ = $unitRow['cd_categ'];
                                    $lccd_model = $unitRow['cd_model'];
                                    $lccd_rs = $unitRow['rs_size'];
                                    $lccd_ct = $unitRow['carat_no'];
                                    $lccd_grms = $unitRow['grams'];
                                    $lccd_uprc = $unitRow['uprc'];

                                $updateTran = "UPDATE r_itemjwl SET cd_item='$cd_bcode', jw_code='$cd_jwcode', nm_item='$cd_roms',cd_categ='$cd_categ',cd_model='$cd_model',rs_size='$cd_rs',carat_no='$cd_ct',grams='$cd_grms',cd_supp='$cd_supp',uprc='$cd_uprc',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_item='$lcref'";
                                mysqli_query($conn,$updateTran);
                            }
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
?>