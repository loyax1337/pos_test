<?php
if (isset($_POST['txtnm_color'])){
    session_start();
    
    include_once 'includes/server.php';

    $lcref = $_SESSION['ref'];
    $nm_color = strtoupper(mysqli_real_escape_string($conn,$_POST['txtnm_color']));
    
    //$status = strtoupper(mysqli_real_escape_string($conn,$_POST['cbostatus']));

    date_default_timezone_set('Asia/Manila');
    $ltt_stamp = date('Y-m-d H:i:s');
    $lccd_user = $_SESSION['id'];
    if(empty($nm_color)){
        echo 'Please input required fields completely.';
    } else {
        $colorSql = "SELECT cd_color FROM r_color WHERE cd_color!='$lcref' AND nm_color='$nm_color'";
        $colorResult = mysqli_query($conn, $colorSql);
        $colorResultCheck = mysqli_num_rows($colorResult);
        if($colorResultCheck > 0) {
            echo "Duplicate record name found.";
        } else {
            $sql = "UPDATE r_color SET nm_color='$nm_color',user_mod='$lccd_user',t_mod='$ltt_stamp' WHERE cd_color='$lcref'";
            mysqli_query($conn,$sql);
        }
        $lcref ='';
    }
    mysqli_close($conn);
} else {
    echo 'Invalid Url Parameters.';
}
?>