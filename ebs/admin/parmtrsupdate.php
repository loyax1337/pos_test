<?php
if(isset($_POST['txtnm_comp']) && isset($_POST['txttin_no']) && isset($_POST['txtadd_comp']) && isset($_POST['txttel_no']) && isset($_POST['txtfax_no']) && isset($_POST['txtemail']) && isset($_POST['txtcurr_yr'])) {
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

    $nm_comp = mysqli_real_escape_string($conn,$_POST['txtnm_comp']);
    $tin_no = mysqli_real_escape_string($conn,$_POST['txttin_no']);
    $add_comp = mysqli_real_escape_string($conn,$_POST['txtadd_comp']);
    $tel_no = mysqli_real_escape_string($conn,$_POST['txttel_no']);
    $fax_no = mysqli_real_escape_string($conn,$_POST['txtfax_no']);
    $email = mysqli_real_escape_string($conn,$_POST['txtemail']);
    $curr_yr = mysqli_real_escape_string($conn,$_POST['txtcurr_yr']);

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if (empty($nm_comp) || empty($curr_yr)){
        echo 'Please input required fields completely.';
    } else {
        $sql = "UPDATE parmtrs SET nm_comp='$nm_comp',tin_no='$tin_no',add_comp='$add_comp',tel_no='$tel_no',fax_no='$fax_no',email='$email',curr_yr='$curr_yr'";
        mysqli_query($conn,$sql);
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>