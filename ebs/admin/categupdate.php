<?php
if(isset($_POST['txtnm_categ'])) {
    session_start();
    
    include_once 'includes/server.php';
    
    $lcref = $_SESSION['ref'];
    $nm_categ = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_categ']));
    
    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if(empty($nm_categ)) {
        echo 'Please input required fields completely.';
    } else {
        $sql = "UPDATE r_categ SET nm_categ='$nm_categ',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_categ='$lcref'";
        mysqli_query($conn,$sql);
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>