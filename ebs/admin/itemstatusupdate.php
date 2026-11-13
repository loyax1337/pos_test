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
        $lccd_user = $_SESSION['id'];
        $lccd_group = $_SESSION['group'];

        if(isset($_POST['cbocd_supp']) && isset($_POST['cbocd_categ']) && isset($_POST['cbocd_model']) && isset($_POST['cbocd_rom']) && isset($_POST['cbostatus'])) {
            include_once 'includes/server.php';
            $lccd_menu = '012';

            $cd_supp = (!empty($_POST['cbocd_supp']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_supp'])) : '');
            $cd_categ = (!empty($_POST['cbocd_categ']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_categ'])) : '');
            $cd_model = (!empty($_POST['cbocd_model']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_model'])) : '');
            $cd_rom = (!empty($_POST['cbocd_rom']) ? base64_decode(mysqli_real_escape_string($conn,$_POST['cbocd_rom'])) : '');
            $status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

            date_default_timezone_set('Asia/Manila');
            $ltt_stamp = date('Y-m-d H:i:s');
            $itemSql = "SELECT cd_item, cd_categ, cd_model, cd_rom FROM r_item WHERE cd_categ='$cd_categ' AND cd_model='$cd_model'".(!empty($cd_supp) ? " AND cd_supp='$cd_supp'" : "").(!empty($cd_rom) ? " AND cd_rom='$cd_rom'" : "");
            $itemResult = mysqli_query($conn, $itemSql);
            $itemResultCheck = mysqli_num_rows($itemResult);
            if($itemResultCheck > 0) {
                $updateTran = "UPDATE r_item SET status='$status', user_mod='$lccd_user', t_mod='$ltt_stamp' WHERE cd_categ='$cd_categ' AND cd_model='$cd_model'".(!empty($cd_supp) ? " AND cd_supp='$cd_supp'":"").(!empty($cd_rom) ? " AND cd_rom='$cd_rom'":"");
                mysqli_query($conn, $updateTran);
            } else {
                echo "No records found with your selected details.";
            }
            mysqli_close($conn);
        }
    }
?>