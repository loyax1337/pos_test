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

        if(isset($_POST['txtnm_item']) && isset($_POST['txtuprc'])) {
            include_once 'includes/server.php';
            $lccd_menu = '030';

            $sql = "SELECT * FROM r_groupd WHERE cd_group='$lccd_group' AND cd_menu='$lccd_menu'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck > 0) {
                $row = mysqli_fetch_assoc($result);
                $llaction = $row['edit'];
                if($llaction=='1') {
                    $nm_item = strtoupper(mysqli_real_escape_string($conn, $_POST['txtnm_item']));
                    $uprc = (empty($_POST['txtuprc']) ? 0.00 : $_POST['txtuprc']);

                    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

                    date_default_timezone_set('Asia/Manila');
                    $ltt_stamp = date('Y-m-d H:i:s');
                    if(empty($nm_item)) {
                        echo "Please enter Name.";
                    } else {
                        if($uprc <= 0) {
                            echo "Please enter a valid Price.";
                        } else {
                            $itemadSql = "SELECT nm_item FROM r_item WHERE LEFT(cd_item,2)='AD' AND cd_item!='$lcref' AND nm_item='$nm_item'";
                            $itemadResult = mysqli_query($conn, $itemadSql);
                            $itemadResultCheck = mysqli_num_rows($itemadResult);
                            if($itemadResultCheck > 0) {
                                echo "Duplicate Name.";
                            } else {
                                $updateTran = "UPDATE r_item SET nm_item='$nm_item', uprc='$uprc', user_mod='$lccd_user', t_mod='$ltt_stamp' WHERE cd_item='$lcref'";
                                mysqli_query($conn, $updateTran);
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