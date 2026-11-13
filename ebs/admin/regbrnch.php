<?php
if (isset($_POST['txtcd_brnch']) && isset($_POST['txtnm_brnch']) && isset($_POST['txtadd_brnch'])){
    session_start();
    include_once 'includes/server.php';

    $cd_brnch = strtoupper(mysqli_real_escape_string($conn,$_POST['txtcd_brnch']));
    $nm_brnch = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_brnch']));
    $add_brnch = strtoupper(mysqli_real_escape_string($conn,$_POST['txtadd_brnch']));
    
    $lccd_user = $_SESSION['id'];
    $lccd_group = $_SESSION['group'];

    date_default_timezone_set('Asia/Manila');
    $ltt_add = date('Y-m-d H:i:s');
    if (empty($cd_brnch) || empty($nm_brnch) || empty($add_brnch)){
        echo 'Please input required fields completely.';
    } else {
        $status = 'A';
        $sql = "INSERT INTO r_brnch(cd_brnch,nm_brnch,add_brnch,t_add,user_add,status) VALUES('$cd_brnch','$nm_brnch','$add_brnch','$ltt_add','$lccd_user','$status')";
        mysqli_query($conn,$sql);
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>