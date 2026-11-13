<?php
if (isset($_POST['txtnm_model'])){
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
    include_once 'includes/server.php';

    $lcref = $_SESSION['ref'];
    $nm_model = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_model']));
    
    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if(empty($nm_model)){
        echo 'Please input required fields completely.';
    } else {
        $modelSql ="SELECT cd_model FROM r_model WHERE cd_model!='$lcref' AND nm_model='$nm_model'";
        $modelResult = mysqli_query($conn, $modelSql);
        $modelResultCheck = mysqli_num_rows($modelResult);
        if($modelResultCheck > 0 ) {
            echo "Duplicate record name found.";
        } else {
            $sql = "UPDATE r_model SET nm_model='$nm_model',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_model='$lcref'";
            mysqli_query($conn,$sql);
        }
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>