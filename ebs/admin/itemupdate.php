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
        $lccd_brnch = $_SESSION['branch'];
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];
        $lcref = $_SESSION['ref'];

        if(isset($_POST['cbocd_categ']) && isset($_POST['cbocd_unit']) && isset($_POST['cbocd_supp']) && isset($_POST['txtserial_no']) && isset($_POST['txtimei1_no']) && isset($_POST['txtimei2_no']) && isset($_POST['cbostatus']) && isset($_POST['txtauthPIN'])) {
            include_once 'includes/server.php';
            $lccd_menu = '012';

            $cd_categ = (!empty($_POST['cbocd_categ']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_categ'])) : '');
            $cd_unit = (!empty($_POST['cbocd_unit']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_unit'])) : '');
            $cd_supp = (!empty($_POST['cbocd_supp']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_supp'])) : '');
            $serial_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtserial_no']));
            $imei1_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtimei1_no']));
            $imei2_no = strtoupper(mysqli_real_escape_string($conn,$_POST['txtimei2_no']));
            $status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));
            $authPIN = mysqli_real_escape_string($conn, $_POST['txtauthPIN']);

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');
            if(!empty($authPIN)) {
                $itemSql = "SELECT no_otp FROM r_item WHERE cd_item='$lcref'";
                $itemResult = mysqli_query($conn, $itemSql);
                $itemResultCheck = mysqli_num_rows($itemResult);
                if($itemResultCheck > 0) {
                    $itemRow = mysqli_fetch_assoc($itemResult);
                    $lcno_otp = $itemRow['no_otp'];

                    if($lcno_otp == $authPIN) {
                        $unitSql = "SELECT a.cd_unit, a.nm_unit, a.desc_unit, a.cd_model, a.cd_rom, a.cd_color, b.nm_model FROM r_unit a LEFT JOIN r_model b ON a.cd_model=b.cd_model WHERE a.cd_unit='$cd_unit'";
                        $unitResult = mysqli_query($conn, $unitSql);
                        $unitResultCheck = mysqli_num_rows($unitResult);
                        if($unitResultCheck > 0) {
                            $unitRow = mysqli_fetch_assoc($unitResult);
                            $lccd_unit = $unitRow['cd_unit'];
                            $lcnm_unit = $unitRow['nm_unit'];
                            $lcdesc_unit = $unitRow['desc_unit'];
                            
                            $lccd_model = $unitRow['cd_model'];
                            $lcnm_model = $unitRow['nm_model'];
                            $lccd_rom = $unitRow['cd_rom'];
                            $lccd_color = $unitRow['cd_color'];

                            $lcnm_item = $lcnm_unit;
                            $lcdesc_item = $lcdesc_unit;

                            if(empty($serial_no)) {
                                if($cd_categ == 'CT00000004') {
                                    $categSql = "SELECT a.cd_categ, a.nm_categ FROM r_categ a WHERE a.cd_categ='$cd_categ'";
                                    $categResult = mysqli_query($conn, $categSql);
                                    $categResultCheck = mysqli_num_rows($categResult);
                                    if($categResultCheck > 0) {
                                        $updateTran = "UPDATE r_item SET nm_item='$lcnm_item',desc_item='$lcdesc_item',cd_categ='$cd_categ',cd_unit='$lccd_unit',cd_model='$lccd_model',cd_rom='$lccd_rom',cd_color='$lccd_color',cd_supp = '$cd_supp', user_mod='$lccd_user',t_mod='$ltt_stamp',status='$status' WHERE cd_item='$lcref'";
                                        mysqli_query($conn,$updateTran);
                                    }
                                }
                            } else {
                                $llcheck = true;
                                if(strpos(strtoupper($lcnm_model),strtoupper('IPHONE')) != "" && $imei1_no == $imei2_no) {
                                    $llcheck = false;
                                }

                                switch(true) {
                                    case ((strpos(strtoupper($lcnm_model),strtoupper('IPHONE 5')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 6')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 7')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 8')) != "" || (strpos(strtoupper($lcnm_model),strtoupper('IPHONE X')) != "" && strpos(strtoupper($lcnm_model),strtoupper('IPHONE XR')) == "" && strpos(strtoupper($lcnm_model),strtoupper('IPHONE XS')) == "")) && (strlen($imei1_no) != 15)):
                                        $llcheck = false;
                                        $imei2_no = '';
                                        break;
                                    case ((strpos(strtoupper($lcnm_model),strtoupper('IPHONE XR')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE XS')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 1')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 2')) != "" || strpos(strtoupper($lcnm_model),strtoupper('IPHONE 3')) != "") && (strlen($imei1_no) != 15 || strlen($imei2_no) != 15)):
                                        $llcheck = false;
                                        break;
                                    default:
                                        // code...
                                        break;
                                }
                                if($status == 'A') {
                                    if($llcheck) {
                                        $itemSql = "SELECT serial_no FROM r_item WHERE cd_item!='$lcref' AND (serial_no='$serial_no'".(!empty($imei1_no) ? " OR imei1_no='$imei1_no'" : "").(!empty($imei2_no) ? " OR imei2_no='$imei2_no'" : "").")";
                                        $itemResult = mysqli_query($conn, $itemSql);
                                        $itemResultCheck = mysqli_num_rows($itemResult);
                                        if($itemResultCheck <= 0) {
                                            $categSql = "SELECT a.cd_categ, a.nm_categ FROM r_categ a WHERE a.cd_categ='$cd_categ'";
                                            $categResult = mysqli_query($conn, $categSql);
                                            $categResultCheck = mysqli_num_rows($categResult);
                                            if($categResultCheck > 0) {
                                                $updateTran = "UPDATE r_item SET nm_item='$lcnm_item',desc_item='$lcdesc_item',cd_categ='$cd_categ',cd_unit='$lccd_unit',cd_model='$lccd_model',cd_rom='$lccd_rom',cd_color='$lccd_color',serial_no='$serial_no',imei1_no='$imei1_no',imei2_no='$imei2_no',cd_supp = '$cd_supp', user_mod='$lccd_user',t_mod='$ltt_stamp',status='$status' WHERE cd_item='$lcref'";
                                                mysqli_query($conn,$updateTran);
                                            }
                                        }
                                    }
                                } else {
                                    $updateTran = "UPDATE r_item SET status='$status', user_mod='$lccd_user', t_mod='$ltt_stamp' WHERE cd_item='$lcref'";
                                    mysqli_query($conn, $updateTran);
                                }
                            }
                        }
                    } else {
                        echo "Invalid Authorization Code.";
                    }
                } else {
                    echo "Reference ID could not be found.";
                }
            } else {
                echo "Invalid Authorization Code.";
            }
            mysqli_close($conn);
        }
    }
?>