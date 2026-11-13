<?php
if (isset($_POST['txtcd_brnch']) && isset($_POST['txtnm_brnch']) && isset($_POST['txtadd_brnch'])){
    session_start();
    
    include_once 'includes/server.php';

    $lcref = $_SESSION['ref'];
    $cd_brnch = strtoupper(mysqli_real_escape_string($conn,$_POST['txtcd_brnch']));
    $nm_brnch = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_brnch']));
    $add_brnch = strtoupper(mysqli_real_escape_string($conn,$_POST['txtadd_brnch']));
    
    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if (empty($cd_brnch) || empty($nm_brnch) || empty($add_brnch)){
        echo 'Please input required fields completely.';
    } else {
        $sql = "UPDATE r_brnch SET cd_brnch='$cd_brnch',nm_brnch='$nm_brnch',add_brnch='$add_brnch',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_brnch='$lcref'";
        mysqli_query($conn,$sql);
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>