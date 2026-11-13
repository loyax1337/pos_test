<?php
if(isset($_POST['txtnm_supp']) && isset($_POST['txtadd_supp']) && isset($_POST['txtno_contact1'])) {
    session_start();
    $lcref = $_SESSION['ref'];
    $lccd_user = $_SESSION['id'];
    include_once 'includes/server.php';
    
    //saveSession();

    $nm_supp = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_supp']));
    $add_supp = strtoupper(mysqli_real_escape_string($conn,$_POST['txtadd_supp']));
    $no_contact1 = mysqli_real_escape_string($conn,$_POST['txtno_contact1']);
    
    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    
    if (empty($nm_supp)){
        echo 'Please input required fields completely.';
    } else {
        $sql = "UPDATE r_supp SET nm_supp='$nm_supp',add_supp='$add_supp',no_contact1='$no_contact1',t_mod='$ltt_stamp',user_mod='$lccd_user' WHERE cd_supp='$lcref'";
        mysqli_query($conn,$sql);
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    //clearSession();
    echo 'Invalid Url Parameters.';
}
?>