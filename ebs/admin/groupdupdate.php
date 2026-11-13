<?php
if (isset($_POST['chkaccess']) || isset($_POST['chkadd']) || isset($_POST['chkedit']) || isset($_POST['chkdel'])){
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
    
    //saveSession();
    $lcref1 = $_SESSION['ref1'];
    $lcref2 = $_SESSION['ref2'];
    $access = mysqli_real_escape_string($conn,$_POST['chkaccess']);
    $add = mysqli_real_escape_string($conn,$_POST['chkadd']);
    $edit = mysqli_real_escape_string($conn,$_POST['chkedit']);
    $del = mysqli_real_escape_string($conn,$_POST['chkdel']);
    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    //if (empty($nm_group)){
        //echo 'Please input required fields completely.';
    //} else {
        $sql = "UPDATE r_groupd a SET a.access='$access',a.add='$add',a.edit='$edit',a.del='$del',a.t_mod='$ltt_stamp' WHERE a.cd_group='$lcref1' AND a.cd_menu='$lcref2'";
        mysqli_query($conn,$sql);
        $lcref1 ='';
        $lcref2 ='';
    //}
    mysqli_close($conn);
} else {
    //clearSession();
    echo 'Invalid Url Parameters.';
}
?>